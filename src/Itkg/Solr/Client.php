<?php

namespace Itkg\Solr;

use Solarium\Client as BaseClient;

/**
 * Client Solr
 *
 * @author Johann THETARD <johann.thetard@businessdecision.com>
 *
 * @package \Itkg\Solr
 */
class Client extends BaseClient
{
    protected $options_itkg = array();

    /**
     * Constructeur
     *
     * @param string $config
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    public function __getLastRequest()
    {
        return $this->getAdapter()->getZendHttp()->getLastRequest();
    }

    public function __getLastResponse()
    {
        return $this->getAdapter()->getZendHttp()->getLastResponse();
    }

    /**
     * Méthode commune aux appels à solr
     *
     *
     * @param string $method
     * @param array $datas
     *      les données envoyés à solr sont de natures différentes selon la méthode :
     *             - ADD_DOCUMENT : les données de reférencement d'un document
     *             - DELETE_DOCUMENT : l'identifiant unique du document à supprimer
     *             - SEARCH_DOCUMENT_BY_FILTERS : les données de filtrage de la requete
     * @param array $options (Les options possibles)
     *
     * @return array()
     */
    public function call($method, $datas = array(), $options = array())
    {
        $response = null;
        $this->addOptions($options);
        $this->setAdapter('Solarium\Core\Client\Adapter\ZendHttp');

        switch($method)
        {
            case 'ADD_DOCUMENT':
                $response = $this->addDocIntoIndex($datas);
                $aResponseDatas['status'] = $response;
                break;
            case 'DELETE_DOCUMENT':
                $response = $this->deleteDocFromIndex($datas['id']);
                $aResponseDatas['status'] = $response;
                break;
            case 'SEARCH_DOCUMENT_BY_FILTERS':
                $aResponseDatas = $this->searchDocByFilters($datas);
                break;
        }

        if ($aResponseDatas) {
            return $aResponseDatas;
        }

        return null;
    }


    /**
     * Ajoute un document dans l'index
     *
     * @param array $datas Les données de reférencement d'un document
     * @return string Statut
     */
    public function addDocIntoIndex($datas)
    {
        // get an update query instance
        $update = $this->createUpdate();

        // create a new document for the data
        $doc = $update->createDocument();

        foreach ($datas as $key => $value) {
            if (is_array($value)) {
                //cas d'un champ multivaleur
                foreach ($value as $value2) {
                    $doc->addField($key, $value2);
                }
            } else {
                $doc->addField($key, $value);
            }
        }

        // add the documents (overwrite existing document) and a commit command to the update query
        $update->addDocument($doc);
        $update->addCommit();

        // this executes the query and returns the result
        $oResult = $this->update($update);

        return $oResult->getStatus();
    }

    /**
     * Supprime un document de l'index
     *
     * @param int $id L'identifiant unique du document à supprimer
     * @return string Statut
     */
    public function deleteDocFromIndex($id)
    {
        // get an update query instance
        $update = $this->createUpdate();

        // add the delete id and a commit command to the update query
        $update->addDeleteById($id);
        $update->addCommit();

        // this executes the query and returns the result
        $oResult = $this->update($update);

        return $oResult->getStatus();
    }

    /**
     * Recherche de documents avec filtres
     *
     * @param array $solrQueryString les données de filtrage de la requete
     * @param array $option Options de recherche possibles
     * @return array $resultset Résultat de la recherche
     */
    public function searchDocByFilters($solrQueryString)
    {
        // get a select query instance
        $query = $this->createSelect();

        $query->setQuery($solrQueryString);

        if (isset($this->options_itkg['start']) && $this->options_itkg['start']!='') {
            $query->setStart($this->options_itkg['start']);
        }

        if (isset($this->options_itkg['rows']) && $this->options_itkg['rows']!='') {
            $query->setRows($this->options_itkg['rows']);
        }

        if (isset($this->options_itkg['sort'])) {
            $aSort = $this->options_itkg['sort'];
        }

        if (is_array($aSort)) {
            // sort the results
            foreach($aSort as $key=>$value) {
                switch($value)
                {
                    case "ASC" :  $query->addSort($key, $query::SORT_ASC); break;
                    case "DESC" : $query->addSort($key, $query::SORT_DESC); break;
                }
            }
        }

        if (isset($this->options_itkg['response_format']) && $this->options_itkg['response_format']!='') {
            $query->setResponseWriter($this->options_itkg['response_format']);
        }

        // this executes the query and returns the result
        $resultset = $this->select($query);

        if ($this->options_itkg['response_format'] == 'phps') {
            $return = unserialize($resultset->getResponse()->getBody());
            $return['responseHeader']['status'] = $resultset->getResponse()->getStatusCode();
            return $return;
        }

        return $resultset->getResponse()->getBody();
    }


    /**
     * Ajoute des options à celles déja existantes
     *
     * @param array $options
     */
    public function addOptions(array $options = array())
    {
        $this->options_itkg = array_merge($this->options_itkg, $options);
    }
}
