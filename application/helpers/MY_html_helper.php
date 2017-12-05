<?php

function matrix_to_html($matrix){
    $html='<table>';
    foreach($matrix as $row){
        $html.='<tr>';
        foreach($row as $data){
            $html.='<td>'.$data.'</td>';
        }
        $html.='</tr>';
    } 
    $html.='</table>';
    return $html;
}
function fecha_hoy(){
        $date = new DateTime();
        $result = $date->format('d-m-Y');
        return $result;
}

