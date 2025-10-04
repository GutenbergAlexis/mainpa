<?php

class cuotasCredito {
	
    private $_id;
    private $_id_comprobante;
    private $_com_fecha_cuota;
    private $_com_monto_cuota;
	
    public function __construct($id, $idComprobante, $comFechaCuota, $comMontoCuota){
        $this->_id              = $id;
        $this->_id_comprobante  = $idComprobante;
        $this->_com_fecha_cuota = $comFechaCuota;
        $this->_com_monto_cuota = $comMontoCuota;
    }

    public function setId($id){
        $this->_id = $id;
    }
    public function getId(){
        return $this->_id;
    }
    public function setIdComprobante($idComprobante){
        $this->_id_comprobante = $idComprobante;
    }
    public function getIdComprobante(){
        return $this->_id_comprobante;
    }
    public function setComFechaCuota($comFechaCuota){
        $this->_com_fecha_cuota = $comFechaCuota;
    }
    public function getComFechaCuota(){
        return $this->_com_fecha_cuota;
    }
    public function setComMontoCuota($comMontoCuota){
        $this->_com_monto_cuota = $comMontoCuota;
    }
    public function getComMontoCuota(){
        return $this->_com_monto_cuota;
    }
}
?>