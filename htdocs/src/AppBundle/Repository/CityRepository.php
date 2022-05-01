<?php

namespace AppBundle\Repository;

/**
 * CityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CityRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Search City by city_name.
     *
     * @param $city_name
     * @param boolean $to_select2_array
     * @param int $offset
     * @param int $limit
     * @return array|mixed
     */
    public function searchCityByCityName($city_name, $to_select2_array = false, $offset = 0, $limit = 20){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c')
            ->from('AppBundle:City', 'c');
        $qb->addSelect("(CASE WHEN c.city_name = :search THEN 3 WHEN c.city_name like :search_first THEN 2 WHEN c.city_name like :global_search THEN 1 ELSE 0 END) AS HIDDEN ORD ");
        $qb->where('c.city_name LIKE :city_name');
        $qb->setParameter('city_name', '%' . $city_name . '%');
        $qb->setParameter('search', $city_name);
        $qb->setParameter('search_first', $city_name . '%');
        $qb->setParameter('global_search', '%' . $city_name . '%');
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);
        $qb->addOrderBy('ORD', 'DESC');
        $qb->addOrderBy('c.city_name', 'ASC');
        $cities = $qb->getQuery()
            ->getResult();
        if($to_select2_array){
            $ret = array();
            foreach ($cities as $city){
                $ret[] = $city->toSelect2Array();
            }
            return $ret;
        }
        return $cities;
    }
}