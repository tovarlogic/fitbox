<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Exercise_model extends CI_Model
{
    //DATABASE
    private $ebTable = 'exercise_basics';
    private $ecTable = 'exercise_contractions';
    private $ematTable = 'exercise_materials';
    private $emechTable = 'exercise_mechanics';
    private $emovTable = 'exercise_movements';
    private $emusTable = 'exercise_muscles';
    private $esTable = 'exercise_sports';
    private $etarTable = 'exercise_targets';
    private $etypTable = 'exercise_types';
    private $evTable = 'exercise_variations';

    private $evbTable = 'exercise_variations_basics';
    private $evcTable = 'exercise_variations_contractions';
    private $evmatTable = 'exercise_variations_materials';
    private $evmechTable = 'exercise_variations_mechanics';
    private $evmovTable = 'exercise_variations_movements';
    private $evmpTable = 'exercise_variations_muscles_primary';
    private $evmsTable = 'exercise_variations_muscles_secondary';
    private $evtarTable = 'exercise_variations_targets';
    private $evtypTable = 'exercise_variations_types';

    private $relations = null;

    private $parameters = null;


    //INITIALIZATION
    function __construct()
    {
        parent::__construct();

        $this->relations = array(
            'basic' => array('db_table' => $this->evbTable, 'id' => 'id_basic'),
            'contraction' => array('db_table' => $this->evcTable, 'id' => 'id_contraction'),
            'material' => array('db_table' => $this->evmatTable, 'id' => 'id_material'),
            'mechanic' => array('db_table' => $this->evmechTable, 'id' => 'id_mechanic'),
            'movement' => array('db_table' => $this->evmovTable, 'id' => 'id_movement'),
            'muscles_primary' => array('db_table' => $this->evmpTable, 'id' => 'id_muscle'),
            'muscles_secondary' => array('db_table' => $this->evmsTable, 'id' => 'id_muscle'),
            'target' => array('db_table' => $this->evtarTable, 'id' => 'id_target'),
            'type' => array('db_table' => $this->evtypTable, 'id' => 'id_type')
        );

        $this->parameters = array(
            'basics' => $this->ebTable,
            'contractions' => $this->ecTable,
            'materials' => $this->ematTable,
            'mechanics' => $this->emechTable,
            'movements' => $this->emovTable,
            'muscles' => $this->emusTable,
            'targets' => $this->etarTable,
            'types' => $this->etypTable
        );

    }

/////////////////////
// VARIABLES
/////////////////////

    function getRelations()
    {   
        $rel = array();
        foreach ($this->relations as $key => $value) 
        {
            $rel[] = $key;
        }
        return $rel;
    }

    function getParameters()
    {
        $par = array();
        foreach ($this->parameters as $key => $value) 
        {
            $par[] = $key;
        }
        return $par;
    }

    function getVariationRelations($id, $rel)
    {
        return $this->db->get_where($this->relations[$rel]['db_table'], array('id_variation' => $id))->result_array();
    }

    function getParameter($param)
    {
        return $this->db->from($this->parameters[$param])->get()->result_array();
    }


//////////////////////////////////////////////////////
//  CREATES
/////////////////////////////////////////////////////
    function setBasicExercise($params)
    {
        return $this->db->insert($this->ebTable, $params);
    }

    function setMaterial($params)
    {
        return $this->db->insert($this->ematTable, $params);
    }

    function setType($params)
    {
        return $this->db->insert($this->etypTable, $params);
    }

    function setSport($params)
    {
        return $this->db->insert($this->esTable, $params);
    }

    /* Function: setVariation 
        Inserts exercise_variation info and inserts data in the rest of related database tables...
        
        Parameters:


        Returns: 
            (int) Id on the exercise created or (bool)FALSE in case of error

    */
    function setVariation($params, $basic, $muscles_primary, $muscles_secondary, $target, $type, $mechanic, $material, $movement, $contraction)
    {
        $this->db->trans_start();
            if($this->db->insert($this->evTable, $params))
            {
                $id = $this->db->insert_id(); 

                foreach ($this->relations as $key => $value) 
                {
                    $data = array();
                    if($$key != null)
                    {
                        foreach ($$key as $k => $v) 
                        {
                            $data[$this->relations[$key]['id']] = $v;
                            $data['id_variation'] = $id;
                            $this->db->insert($this->relations[$key]['db_table'], $data);
                        }
                    }
                }
            }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return FALSE;
        }
        else
        {
            return $id;
        }
    }

//////////////////////////////////////////////////////
//  READS
/////////////////////////////////////////////////////
    function getBasicExercise($id)
    {
        return $this->db->from($this->ebTable)->where('id', $id)->get()->row();
    }

    function getBasicExercises()
    {
        return $this->db->from($this->ebTable)->get()->result();
    }

    function getMaterial($id)
    {
        return $this->db->from($this->ematTable)->where('id', $id)->get()->row();
    }

    function getMaterials()
    {
        return $this->db->from($this->ematTable)->get()->result();
    }

    function getType($id)
    {
        return $this->db->from($this->etypTable)->where('id', $id)->get()->row();
    }

    function getTypes()
    {
        return $this->db->from($this->etypTable)->get()->result();
    }

    function getSport($id)
    {
        return $this->db->from($this->esTable)->where('id', $id)->get()->row();
    }

    function getSports()
    {
        return $this->db->from($this->esTable)->get()->result();
    }

    /* Function: getVariation 
        Gets one variation exercise. If needed will return all its related info as well.
        
        Parameters:
            $id (int) - The requested variation Id.
            $relations (boolean) - default = FALSE. If TRUE apart from the variation detail will also return all its related info.

        Returns: 
            Multidimentional Array

    */
    function getVariation($id, $relations = FALSE)
    {
        $variation = $this->db->from($this->evTable)->where('id', $id)->get();
        if($variation !== FALSE && $variation->num_rows() > 0)
        {
            if($relations === TRUE)
            {
                $data = array('variation' => $variation->row());
                foreach ($this->relations as $k => $v) 
                {
                    $result = $this->db->from($this->relations[$k]['db_table'])->where('id_variation', $id)->get();
                    if($result !== FALSE && $result->num_rows() > 0)
                    {
                        $data[$k] = $result->result_array();
                    } 
                }
                return $data;
            }

            return $variation;
        }
        else
            return FALSE;
        
    }

    /* Function: getVariations 
        Gets all variation exercises including each related info such as mechanics, movements, etc...
        
        Parameters:
            $filter - not yet implemented

        Returns: 
            Multidimentional Array

    */
    function getVariations($filter = null)
    {
        $variations = $this->db->from($this->evTable)->get();

        if($variations !== FALSE && $variations->num_rows() > 0)
        {
            $variations = $variations->result_array();

            foreach ($variations as $key => $var) 
            {
                foreach ($this->relations as $k => $v) 
                {
                    if($k == 'muscles_primary' OR $k == 'muscles_secondary') 
                        $param = 'muscles';
                    else
                        $param = $k.'s';


                    $result = $this->db->select('t1.'.$this->relations[$k]['id'].', t2.*')
                                        ->from($this->relations[$k]['db_table'].' t1')
                                        ->join($this->parameters[$param].' t2', 't2.id = t1.'.$this->relations[$k]['id']) 
                                        ->where('t1.id_variation =', $var['id'])
                                        ->get();

                    if($result !== FALSE && $result->num_rows() > 0)
                    {
                        $variations[$key][$k] = $result->result_array();
                    }
                }
            }
            return $variations;
        }

        return FALSE;


        // if($filter != null)
        // {
        //     foreach ($filter as $key => $value) 
        //     {
        //         $query = $this->relations[$key]['db_table'].'.'.$this->relations[$key]['id'];
        //         $this->db->->where_in($query, $value);
        //     }
        // }

    }

    

///////////////////////////////////////////
// UPDATES
// ///////////////////////////////////////

    function editBasicExercise($id, $params)
    {

        return $this->db->where('id', $id)->update($this->ebTable, $params);
    }

    function editMaterial($id, $params)
    {

        return $this->db->where('id', $id)->update($this->ematTable, $params);
    }

    function editType($id, $params)
    {

        return $this->db->where('id', $id)->update($this->etypTable, $params);
    }

    function editSport($id, $params)
    {

        return $this->db->where('id', $id)->update($this->esTable, $params);
    }

    /* Function: editVariation 
        Updates the data of any variation exercise and of database tables relating to it, such as Mechanics, movements, etc...
        
        Parameters:
            $id - exercise_variation primary id
            $rel - characteristic to edit
            $data - (int) or (array) of the characteristics primari id/s to relate with

        Returns: 
            Boolean

    */
    function editVariation($id, $params, $basic, $muscles_primary, $muscles_secondary, $target, $type, $mechanic, $material, $movement, $contraction)
    {
        $this->db->trans_start();
            $this->db->where('id', $id)->update($this->evTable, $params);

            foreach ($this->relations as $key => $value) 
            {
                //$this->db->delete($this->relations[$key]['db_table'], array('id_variation' => $id));
                if(!empty($$key) AND sizeof($$key) > 0)
                 {
                    $this->editVariationRelations($id, $key, $$key);
                 }
            }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }

    }

    /* Function: editVariationRelations 
        Updates the data in tables relating excercice_variations and other exercise characteristics such as Mechanics, movements, etc...
        
        Parameters:
            $id - exercise_variation primary id
            $rel - characteristic to edit
            $data - (int) or (array) of the characteristics primari id/s to relate with

        Returns: 
            Boolean

    */
    function editVariationRelations($id, $rel, $data)
    {
        $errors = 0;
        
        if($this->db->get_where($this->relations[$rel]['db_table'], array('id_variation' => $id))->row())
        {
            $result = $this->db->select($this->relations[$rel]['id'])->get_where($this->relations[$rel]['db_table'], array('id_variation' => $id))->result();
            foreach ($result as $res) 
            {
                $x = $this->relations[$rel]['id'];
                $data_db[] = $res->$x;
            }
            
            $add = array_diff($data, $data_db); //new relations to insert
            foreach ($add as $key => $value) 
            {
                if(!$this->db->insert($this->relations[$rel]['db_table'], array('id_variation' => $id, $this->relations[$rel]['id'] => $value)))
                  $errors++;
            }

            $del = array_diff($data_db, $data); //old relations to delete
            foreach ($del as $key => $value) 
            {
                if(!$this->db->delete($this->relations[$rel]['db_table'], array('id_variation' => $id, $this->relations[$rel]['id'] => $value)))
                  $errors++;
            }
        }
        else
        {
            foreach ($data as $key => $value) {
                if(!$this->db->insert($this->relations[$rel]['db_table'], array('id_variation' => $id, $this->relations[$rel]['id'] => $value)))
                  $errors++;
            }
        }
        if($errors == 0) 
            return TRUE;
        else 
            return FALSE;
    }


///////////////////////////////////////////
// DELETES
// ///////////////////////////////////////

    function deleteBasicExercise($id)
    {

        return $this->db->where('id', $id)->delete($this->ebTable);
    }

    function deleteMaterial($id)
    {

        return $this->db->where('id', $id)->delete($this->ematTable);
    }

    function deleteType($id)
    {

        return $this->db->where('id', $id)->delete($this->etypTable);
    }

    function deleteSport($id)
    {

        return $this->db->where('id', $id)->delete($this->esTable);
    }

    function deleteVariation($id)
    {
        $this->db->trans_start();
            foreach ($this->relations as $key => $value) 
            {
                $this->db->delete($this->relations[$key]['db_table'], array('id_variation' => $id));
            }

            $this->db->delete($this->evTable, array('id' => $id));
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }

    }

}   

?>