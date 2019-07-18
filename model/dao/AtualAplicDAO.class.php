<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ('./dbutil/Conn.class.php');
/**
 * Description of AtualizaAplicDAO
 *
 * @author anderson
 */
class AtualAplicDAO extends Conn {
    //put your code here

    /** @var PDOStatement */
    private $Read;

    /** @var PDO */
    private $Conn;

    public function verAtualAplic($dados) {

        foreach ($dados as $d) {

            $celular = $d->idCelularAtual;
            $va = $d->versaoAtual;
        }

        $retorno = 'N';

        $select = "SELECT "
                . " COUNT(*) AS QTDE "
                . " FROM "
                . " PCI_ATUALIZACAO "
                . " WHERE "
                . " NUMERO = " . $celular;

        $this->Conn = parent::getConn();
        $this->Read = $this->Conn->prepare($select);
        $this->Read->setFetchMode(PDO::FETCH_ASSOC);
        $this->Read->execute();
        $result = $this->Read->fetchAll();

        foreach ($result as $item) {
            $v = $item['QTDE'];
        }

        if ($v == 0) {

            $sql = "INSERT INTO PCI_ATUALIZACAO ("
                    . " NUMERO "
                    . " , VERSAO_ATUAL "
                    . " , VERSAO_NOVA "
                    . " , DTHR_ULT_ATUAL "
                    . " ) "
                    . " VALUES ("
                    . " " . $celular
                    . " , TRIM(TO_CHAR(" . $va . ", '99999999D99')) "
                    . " , TRIM(TO_CHAR(" . $va . ", '99999999D99')) "
                    . " , SYSDATE "
                    . " )";

            $this->Create = $this->Conn->prepare($sql);
            $this->Create->execute();
            
        } else {

            $select = " SELECT "
                    . " VERSAO_NOVA "
                    . " , VERSAO_ATUAL"
                    . " FROM "
                    . " PCI_ATUALIZACAO "
                    . " WHERE "
                    . " NUMERO = " . $celular;

            $this->Read = $this->Conn->prepare($select);
            $this->Read->setFetchMode(PDO::FETCH_ASSOC);
            $this->Read->execute();
            $result = $this->Read->fetchAll();

            foreach ($result as $item) {
                $vn = $item['VERSAO_NOVA'];
                $vab = $item['VERSAO_ATUAL'];
            }

            if ($va != $vab) {

                $sql = "UPDATE PCI_ATUALIZACAO "
                        . " SET "
                        . " VERSAO_ATUAL = TRIM(TO_CHAR(" . $va . ", '99999999D99'))"
                        . " , VERSAO_NOVA = TRIM(TO_CHAR(" . $va . ", '99999999D99'))"
                        . " , DTHR_ULT_ATUAL = SYSDATE "
                        . " WHERE "
                        . " NUMERO = " . $celular;

                $this->Create = $this->Conn->prepare($sql);
                $this->Create->execute();
                
            } else {
            
                if ($va != $vn) {
                    $retorno = 'S';
                } else {

                    if (strcmp($va, $vab) <> 0) {

                        $sql = "UPDATE PBM_ATUALIZACAO "
                                . " SET "
                                . " VERSAO_ATUAL = TRIM(TO_CHAR(" . $va . ", '99999999D99'))"
                                . " , DTHR_ULT_ATUAL = SYSDATE "
                                . " WHERE "
                                . " NUMERO = " . $celular;

                        $this->Create = $this->Conn->prepare($sql);
                        $this->Create->execute();
                        
                    }
                }
            }
        }

        return $retorno;
    }

}
