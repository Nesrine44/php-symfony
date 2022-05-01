<?php

namespace PrAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Swagger\Annotations as OA;


class AzureController extends Controller
{
    /**
     * Link to this controller to start the Azure AD "connect" process.
     *
     *
     * @Route("/connect/azure", name="connect_azure_start", methods={"GET"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=302,
     *          description="Launch Azure AD connection redirecting user to Microsoft services."
     *      )
     * )
     *
     * @OA\Tag(name="Azure AD endpoints")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectAction()
    {
        return $this->get('oauth2.registry')
            ->getClient('azure')
            ->redirect($this->container->getParameter('azure.scope'));
    }

    /**
     * Redirection url from Azure AD wich redirect itself to login page or homepage depending on the case.
     *
     * @Route("/connect/azure/check", name="connect_azure_check", methods={"GET","POST"})
     * @OA\Get(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=302,
     *          description="Error: redirection to login page."
     *      ),
     *      @OA\Parameter(
     *          name="error",
     *          in="path",
     *          type="string",
     *          required=false,
     *          description="Title of the error"
     *      ),
     *      @OA\Parameter(
     *          name="error_description",
     *          in="path",
     *          type="string",
     *          required=false,
     *          description="Error description"
     *      )
     * )
     * @OA\Post(
     *      produces={"text/html"},
     *      @OA\Response(
     *          response=302,
     *          description="Success: redirection to homepage"
     *      ),
     *      @OA\Parameter(
     *          name="access_token",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="Access token"
     *      ),
     *      @OA\Parameter(
     *          name="token_type",
     *          in="query",
     *          type="string",
     *          required=true,
     *          description="Token type"
     *      )
     * )
     * @OA\Tag(name="Azure AD endpoints")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectCheckAction()
    {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)
        $request = Request::createFromGlobals();
        $url_redirect = '/';
        $userInfo = null;
        $error = ($request->query->get('error')) ? $request->query->get('error') : $request->request->get('error');
        $error_description = ($request->query->get('error_description')) ? $request->query->get('error_description') : $request->request->get('error_description');
        $my_portal_authentication = $this->container->getParameter('pr_auth')['my_portal_authentication_enabled'];
        try {
            $access_token = $request->request->get('access_token');
            $token_type = $request->request->get('token_type');
            if($access_token && $token_type){
                $userInfo = self::getUserInfo($access_token, $token_type);
            }
        } catch (\Exception $e) {
            $error = '500';
            $error_description = $e->getMessage();
        }
        if($userInfo && array_key_exists('error', $userInfo)){
            $error = '500';
            $error_description = $userInfo['error']['message'];
        }
        if ($error || $error_description) {
            $this->addFlash(
                'error',
                $error . ' - ' . $error_description
            );
        } elseif ($my_portal_authentication && $userInfo) {
            $email = array_key_exists('mail', $userInfo) ? $userInfo['mail'] : null;
            $name = array_key_exists('displayName', $userInfo) ? $userInfo['displayName'] : null;
            $firstname = array_key_exists('givenName', $userInfo) ? $userInfo['givenName'] : $name;
            $lastname = array_key_exists('surname', $userInfo) ? $userInfo['surname'] : null;
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')->createPernodRicardUser($this->container, $email, $firstname, $lastname, true);
            if ($user) {
                // The third parameter "main" can change according to the name of your firewall in security.yml
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->get('security.token_storage')->setToken($token);

                // If the firewall name is not main, then the set value would be instead:
                // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));
                $this->get('session')->set('_security_main', serialize($token));

                // Fire the login event manually
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
            }
            $key = '_security.main.target_path'; #where "main" is your firewall name
            if ($this->container->get('session')->has($key)) {
                //set the url based on the link they were trying to access before being authenticated
                $url_redirect = $this->container->get('session')->get($key);
                //remove the session key
                $this->container->get('session')->remove($key);
            }
        }
        return $this->redirect($url_redirect);
    }


    /**
     * Get user info.
     *
     * @param string $access_token
     * @param string $token_type
     * @return array|null
     */
    public static function getUserInfo($access_token, $token_type){
        try{
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Authorization: '.$token_type.' '.$access_token,
            ));
            curl_setopt($curl, CURLOPT_URL, 'https://graph.microsoft.com/v1.0/me');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($curl);
            curl_close($curl);
            if($result){
                return json_decode($result, true);
            }
            return $result;
        }catch (\Exception $e){
            return null;
        }
    }
}
