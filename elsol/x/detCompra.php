<?php

class detCompra {
	
    private $_id;
    private $_id_compra;
    private $_id_producto;
	private $_pro_codigo;
    private $_pro_descripcion;
    private $_pro_unidad_medida;
    private $_pro_codigo_unidad_medida;
    private $_pro_cantidad;
    private $_pro_costo_unitario;
    private $_pro_costo_total;
	
    public function __construct($id, $idCompra, $idProducto, $proCodigo, $proDescripcion, $proUnidadMedida, $proCodigoUnidadMedida, $proCantidad, $proCostoUnitario, $proCostoTotal){
        $this->_id                       = $id;
        $this->_id_compra                = $idCompra;
        $this->_id_producto              = $idProducto;
        $this->_pro_codigo               = $proCodigo;
        $this->_pro_descripcion          = $proDescripcion;
        $this->_pro_unidad_medida        = $proUnidadMedida;
        $this->_pro_codigo_unidad_medida = $proCodigoUnidadMedida;
        $this->_pro_cantidad             = $proCantidad;
        $this->_pro_costo_unitario       = $proCostoUnitario;
        $this->_pro_costo_total          = $proCostoTotal;
    }

    public function setId($id){
        $this->_id = $id;
    }
    public function getId(){
        return $this->_id;
    }
    public function setIdCompra($idCompra){
        $this->_id_compra = $idCompra;
    }
    public function getIdCompra(){
        return $this->_id_compra;
    }
    public function setIdProducto($idProducto){
        $this->_id_producto = $idProducto;
    }
    public function getIdProducto(){
        return $this->_id_producto;
    }
    public function setProCodigo($proCodigo){
        $this->_pro_codigo = $proCodigo;
    }
    public function getProCodigo(){
        return $this->_pro_codigo;
    }
    public function setProDescripcion($proDescripcion){
        $this->_pro_descripcion = $proDescripcion;
    }
    public function getProDescripcion(){
        return $this->_pro_descripcion;
    }
    public function setProUnidadMedida($proUnidadMedida){
        $this->_pro_unidad_medida = $proUnidadMedida;
    }
    public function getProUnidadMedida(){
        return $this->_pro_unidad_medida;
    }
    public function setProCodigoUnidadMedida($proCodigoUnidadMedida){
        $this->_pro_codigo_unidad_medida = $proCodigoUnidadMedida;
    }
    public function getProCodigoUnidadMedida(){
        return $this->_pro_codigo_unidad_medida;
    }
    public function setProCantidad($proCantidad){
        $this->_pro_cantidad = $proCantidad;
    }
    public function getProCantidad(){
        return $this->_pro_cantidad;
    }
    public function setProCostoUnitario($proCostoUnitario){
        $this->_pro_costo_unitario = $proCostoUnitario;
    }
    public function getProCostoUnitario(){
        return $this->_pro_costo_unitario;
    }
    public function setProCostoTotal($proCostoTotal){
        $this->_pro_costo_total = $proCostoTotal;
    }
    public function getProCostoTotal(){
        return $this->_pro_costo_total;
    }
}
?>