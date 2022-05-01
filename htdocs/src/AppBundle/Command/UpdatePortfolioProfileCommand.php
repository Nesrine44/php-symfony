<?php

namespace AppBundle\Command;

use AppBundle\Worker\PernodWorker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdatePortfolioProfileCommand extends ContainerAwareCommand
{

    private $em;
    private $worker;

    public function __construct(EntityManagerInterface $em, PernodWorker $worker)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        parent::__construct();
        $this->em = $em;
        $this->worker = $worker;
    }

    protected function configure()
    {
        $this->setName('pri:update_portfolio')
            ->setDescription('Updating Portfolio Profile.');
    }

    /**
     * Order innovations by entity
     * @param $innovations
     * @return array
     */
    public static function orderInnovationsByEntity($innovations)
    {
        $ret = array();
        foreach ($innovations as $innovation) {
            $ret[$innovation['entity_id']][] = $innovation;
        }
        return $ret;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('logger')->info('[Launch -> pri:update_portfolio]');
        $wsGlobalDatas = $this->getContainer()->get('app.website_global_datas');
        $innovations_array = $wsGlobalDatas->getAllInnovationsArray();
        $products_by_entity = self::orderInnovationsByEntity($innovations_array);

        $used_ids = array();
        $unusable_ids = array();
        $limit_by_entity = 5;
        $nbs = array(
            'big_bet' => 0,
            'nba' => 0,
            'top' => 0,
            'worst' => 0,
            'high' => 0,
            'contributor' => 0,
            'nb_innovations' => count($innovations_array),
            'nb_entity' => count($products_by_entity),
            'excluded' => 0,
            'total_check' => 0,
            'total_usable' => 0,
            'total_used' => 0
        );
        $force_refresh = true;
        $used_stages = array('scale_up');

        foreach ($products_by_entity as $products) {
            $sortArray = array();
            foreach ($products as $proper_product) {
                $sortArray['cumul']['caap'][] = $proper_product['cumul']['caap'];
            }
            array_multisort($sortArray['cumul']['caap'], SORT_DESC, $products);


            foreach ($products as $proper_product) {
                if (in_array($proper_product['current_stage'], $used_stages)) {
                    $nbs['total_usable']++;
                }
                if (!in_array($proper_product['id'], $used_ids) && $proper_product['growth_strategy'] == 'Big Bet') {
                    $used_ids[] = $proper_product['id'];
                    $unusable_ids[] = $proper_product['id'];
                    $nbs['big_bet']++;
                }
                if (!in_array($proper_product['id'], $used_ids) && $proper_product['growth_strategy'] == 'New Business Acceleration') {
                    $used_ids[] = $proper_product['id'];
                    $unusable_ids[] = $proper_product['id'];
                    $nbs['nba']++;
                }
                if (!in_array($proper_product['id'], $used_ids) && $proper_product['growth_strategy'] == 'Top contributor') {
                    $used_ids[] = $proper_product['id'];
                    $unusable_ids[] = $proper_product['id'];
                    $nbs['top']++;
                } elseif (!in_array($proper_product['current_stage'], $used_stages)) {
                    $unusable_ids[] = $proper_product['id'];
                    $nbs['excluded']++;
                } elseif ($proper_product['classification_type'] == 'Service') {
                    $unusable_ids[] = $proper_product['id'];
                    $nbs['excluded']++;
                }
            }


            // Negative CAAP
            array_multisort($sortArray['cumul']['caap'], SORT_ASC, $products); // order by ASC
            $nb = 0;
            $quarterly_value = 'Negative CAAP';
            foreach ($products as $proper_product) {
                if (!in_array($proper_product['id'], $unusable_ids) && $nb < $limit_by_entity && $proper_product['cumul']['caap'] < 0) {
                    $used_ids[] = $proper_product['id'];
                    $unusable_ids[] = $proper_product['id'];
                    if ($force_refresh || $proper_product['growth_strategy'] != $quarterly_value) {
                        $portfolio_profile = $this->em->getRepository('AppBundle:PortfolioProfile')->find(4);
                        $innovation = $this->em->getRepository('AppBundle:Innovation')->findActiveInnovation($proper_product['id']);
                        if ($portfolio_profile && $innovation) {
                            $innovation->setPortfolioProfile($portfolio_profile);
                            $proper_product['growth_strategy'] = $portfolio_profile->getTitle();
                            $proper_product['status'] = $portfolio_profile->getExcelStatus();
                            $this->worker->updateAllInnovationsAndConsolidationByInnovation($proper_product);
                        }
                    }
                    $nbs['worst']++;
                    $nb++;
                }
            }
            $this->em->flush();
            // Then We order by cumul A&P
            $sortArray = array();
            foreach ($products as $proper_product) {
                $sortArray['cumul']['total_ap'][] = $proper_product['cumul']['total_ap'];
            }

            // HIGH INVESTMENT
            array_multisort($sortArray['cumul']['total_ap'], SORT_ASC, $products); // order by DESC
            $nb = 0;
            $quarterly_value = 'High investment';
            foreach ($products as $proper_product) {
                if (!in_array($proper_product['id'], $unusable_ids) && $nb < $limit_by_entity) {
                    $used_ids[] = $proper_product['id'];
                    $unusable_ids[] = $proper_product['id'];
                    if ($force_refresh || $proper_product['growth_strategy'] != $quarterly_value) {
                        $portfolio_profile = $this->em->getRepository('AppBundle:PortfolioProfile')->find(5);
                        $innovation = $this->em->getRepository('AppBundle:Innovation')->findActiveInnovation($proper_product['id']);
                        if ($portfolio_profile && $innovation) {
                            $innovation->setPortfolioProfile($portfolio_profile);
                            $proper_product['growth_strategy'] = $portfolio_profile->getTitle();
                            $proper_product['status'] = $portfolio_profile->getExcelStatus();
                            $this->worker->updateAllInnovationsAndConsolidationByInnovation($proper_product);
                        }
                    }
                    $nbs['high']++;
                    $nb++;
                }
            }
            $this->em->flush();

            // CONTRIBUTOR
            $quarterly_value = 'Contributor';
            foreach ($products as $proper_product) {
                if (!in_array($proper_product['id'], $used_ids)) {
                    $used_ids[] = $proper_product['id'];
                    $nbs['contributor']++;
                    if($force_refresh || $proper_product['growth_strategy'] != $quarterly_value) {
                        $portfolio_profile = $this->em->getRepository('AppBundle:PortfolioProfile')->find(1);
                        $innovation = $this->em->getRepository('AppBundle:Innovation')->findActiveInnovation($proper_product['id']);
                        if ($portfolio_profile && $innovation) {
                            $innovation->setPortfolioProfile($portfolio_profile);
                            $proper_product['growth_strategy'] = $portfolio_profile->getTitle();
                            $proper_product['status'] = $portfolio_profile->getExcelStatus();
                            $this->worker->updateAllInnovationsAndConsolidationByInnovation($proper_product);
                        }
                    }
                }
            }
        }
        $this->em->flush();

        $nbs['total_used'] = $nbs['big_bet'] + $nbs['top'] + $nbs['worst'] + $nbs['high'];
        $nbs['total_check'] = $nbs['total_used'] + $nbs['contributor'];
        
        $watchdog_message = 'Growth strategy updated for '.$nbs['total_check'].'/'.$nbs['nb_innovations'].' innovations and '.$nbs['nb_entity'].' entities : '.$nbs['big_bet'].' Big bets [-] '.$nbs['top'].' Top contributors [-] '.$nbs['worst'].' Negative CAAP [-] '.$nbs['high'].' High Investments [-] '.$nbs['contributor'].' Contributors [-] '.$nbs['excluded'].' excluded [TOTAL USED : '.$nbs['total_used'].'/'.$nbs['total_usable'].']';
        print($watchdog_message);
        $this->getContainer()->get('logger')->info('[End -> pri:update_portfolio]');
    }



}