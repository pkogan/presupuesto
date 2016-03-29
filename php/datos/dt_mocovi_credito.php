<?php
class dt_mocovi_credito extends toba_datos_tabla
{
	function get_listado($where=null)
	{
            if(!is_null($where)){
                $where="where $where";
            }else{
                $where='';
            }
		$sql = "SELECT
			t_mc.id_credito,
			t_mpp.anio as id_periodo_nombre,
			t_ua.descripcion as id_unidad_nombre,
			t_e.descripcion as id_escalafon_nombre,
			t_mtc.tipo as id_tipo_credito_nombre,
			t_mc.descripcion,
			t_mc.credito,
			t_mp.nombre as id_programa_nombre,
                        t_mtp.tipo as tipo_programa
		FROM
			mocovi_credito as t_mc	LEFT OUTER JOIN mocovi_periodo_presupuestario as t_mpp ON (t_mc.id_periodo = t_mpp.id_periodo)
			LEFT OUTER JOIN unidad_acad as t_ua ON (t_mc.id_unidad = t_ua.sigla)
			LEFT OUTER JOIN escalafon as t_e ON (t_mc.id_escalafon = t_e.id_escalafon)
			LEFT OUTER JOIN mocovi_tipo_credito as t_mtc ON (t_mc.id_tipo_credito = t_mtc.id_tipo_credito)
			LEFT OUTER JOIN mocovi_programa as t_mp ON (t_mc.id_programa = t_mp.id_programa)
                        LEFT OUTER JOIN mocovi_tipo_programa as t_mtp ON (t_mp.id_tipo_programa = t_mtp.id_tipo_programa)
                        
$where

		ORDER BY descripcion";
		return toba::db('presupuesto')->consultar($sql);
	}




    static function get_credito_periodo_actual() {
        $sql = "select c.id_unidad as unidad,c.id_escalafon as escalafon,pp.area,pp.sub_area,pp.nombre, 
            sum(credito) as credito
            from mocovi_credito c
                
                inner join mocovi_periodo_presupuestario p on c.id_periodo=p.id_periodo and actual is true
                inner join mocovi_programa pp on c.id_programa=pp.id_programa
                group by c.id_unidad,c.id_escalafon,pp.area,pp.sub_area,pp.nombre
               ";

        $credito_unidad = toba::db()->consultar($sql);

        $credito = array();
        /* costodiacategoria= (basico + zona)*13/360 */
        foreach ($credito_unidad as $row) {
            $credito[$row['unidad']][$row['escalafon']][$row['area']][$row['sub_area']]['credito'] = $row['credito'];
            $credito[$row['unidad']][$row['escalafon']][$row['area']][$row['sub_area']]['nombre'] = $row['nombre'];
        }
        return $credito;
    }
}