<?php

class detComprobante {
	
    private $_id;
    private $_id_comprobante;
    private $_id_producto;
	private $_pro_codigo;
    private $_pro_descripcion;
    private $_pro_unidad_medida;
    private $_pro_codigo_unidad_medida;
    private $_pro_cantidad;
    private $_pro_espesor;
    private $_pro_ancho;
    private $_pro_largo;
    private $_pro_cantidad_final;
    private $_pro_precio_unitario;
    private $_pro_precio_total;
	
    public function __construct($id, $idComprobante, $idProducto, $proCodigo, $proDescripcion, $proUnidadMedida, $proCodigoUnidadMedida, $proCantidad, $proEspesor, $proAncho, $proLargo, $proCantidadFinal, $proPrecioUnitario, $proPrecioTotal){
        $this->_id                       = $id;
        $this->_id_comprobante           = $idComprobante;
        $this->_id_producto              = $idProducto;
        $this->_pro_codigo               = $proCodigo;
        $this->_pro_descripcion          = $proDescripcion;
        $this->_pro_unidad_medida        = $proUnidadMedida;
        $this->_pro_codigo_unidad_medida = $proCodigoUnidadMedida;
        $this->_pro_cantidad             = $proCantidad;
        $this->_pro_espesor              = $proEspesor;
        $this->_pro_ancho                = $proAncho;
        $this->_pro_largo                = $proLargo;
        $this->_pro_cantidad_final       = $proCantidadFinal;
        $this->_pro_precio_unitario      = $proPrecioUnitario;
        $this->_pro_precio_total         = $proPrecioTotal;
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
    public function setProEspesor($proEspesor){
        $this->_pro_espesor = $proEspesor;
    }
    public function getProEspesor(){
        return $this->_pro_espesor;
    }
    public function setProAncho($proAncho){
        $this->_pro_ancho = $proAncho;
    }
    public function getProAncho(){
        return $this->_pro_ancho;
    }
    public function setProLargo($proLargo){
        $this->_pro_largo = $proLargo;
    }
    public function getProLargo(){
        return $this->_pro_largo;
    }
    public function setProCantidadFinal($proCantidadFinal){
        $this->_pro_cantidad_final = $proCantidadFinal;
    }
    public function getProCantidadFinal(){
        return $this->_pro_cantidad_final;
    }
    public function setProPrecioUnitario($proPrecioUnitario){
        $this->_pro_precio_unitario = $proPrecioUnitario;
    }
    public function getProPrecioUnitario(){
        return $this->_pro_precio_unitario;
    }
    public function setProPrecioTotal($proPrecioTotal){
        $this->_pro_precio_total = $proPrecioTotal;
    }
    public function getProPrecioTotal(){
        return $this->_pro_precio_total;
    }
	
    public function insertar(){
        $SQL="insert into Empleado values";
        $SQL.="'','$this->_apellido',$this->_legajo,'$this->_nombre'";
        $bd=new BaseDeDatos();
        $bd->query($SQL);
    }
    public function getAll(){
        $SQL="select * from empleado";
        $bd=new BaseDeDatos();
        $bd->query($SQL);
        $aItems=array();
 
        foreach ($bd->fetchObject() as $f){
            $aItems[]=$f;
        }
	}
}
?>