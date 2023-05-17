<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Nutrition_model extends CI_Model
{
    //DATABASE
    private $nfTable = 'nutrition_foods';
    private $nlTable = 'nutrition_log';
    private $nfgTable = 'nutrition_food_groups';
    private $nfrTable = 'nutrition_foods_requirements';

    //VAR
    public $user_id = null;

    public $food_list = null;

    public $meal_list = array(
        'Breakfast' => 'Breakfast', 
        'Lunch' => 'Lunch', 
        'Snacks' => 'Snacks', 
        'Dinner' => 'Dinner'
    );

    function __construct()
    {
      parent::__construct();
      //$this->translate_DB();
    }
    // INITIALIZATION

    function setUser($user_id)
    {
        $this->user_id=$user_id;
    }

    function setFoodList($food_list)
    {
        $this->food_list=$food_list;
    }

////////////////////////////////////////////////
//  SECTION: DIETS
////////////////////////////////////////////////

    function calcBlocks($fat, $carb, $protein)
    {
        $blocks['fat'] = $fat/3;
        $blocks['carb'] = $carb/9;
        $blocks['protein'] = $protein/7;

        $blocks['total'] = $blocks['fat'] + $blocks['carb'] + $blocks['protein'];

        return $blocks;
    }

    function calcGL($GI, $carb)
    {
        // GI : High >= 70; Medium 56-79; Low <=55 
        // GL : High >= 20; Medium 11-19; Low <=10 
        return $GI*$carb/100;
    }

    function calcMacrosKcal($carb, $protein, $fat)
    {
        $total = $carb*4 + $protein*4 + $fat*9;
        $carb = $carb*4 * 100 / $total;
        $protein = $protein*4 * 100 / $total;
        $fat = $fat*9 * 100 / $total;

        return array(round($carb,1), round($protein,1), round($fat,1));
    }

    
////////////////////////////////////////////
// SECTION: FOODS
/////////////////////////////////////////////
    function getMealList()
    {
        return $this->meal_list;
    }

    function getFoods($cat = null)
    {
        if ($this->food_list == null)
        {
            $fd_list = $this->db->select('id, Shrt_Desc, brand, GmWt_1, GmWt_Desc1, GmWt_2, GmWt_Desc2')->from($this->nfTable)->order_by('Shrt_Desc', 'ASC')->get()->result();

            $food_list = array('' =>'');
            foreach ($fd_list as $list) 
            {
              $food_list[$list->id] = ($list->brand == null) ? $list->Shrt_Desc : $list->Shrt_Desc." (".$list->brand.")";

            }
            $this->setFoodList($food_list);
        }

        return $this->food_list;
    }

    function getFoodsByNutrient($nutrient, $group_id = null)
    {
         $this->db->select('nutrition_foods.id, nutrition_foods.Shrt_Desc, nutrition_foods.brand, nutrition_foods.group_id, nutrition_foods.'.$nutrient.',
                            nutrition_food_groups.name as group')
                            ->from($this->nfTable)
                            ->join($this->nfgTable, 'nutrition_foods.group_id = nutrition_food_groups.id');
                            
        if($group_id != null ) $this->db->where('group_id', $group_id);

        $fd_list = $this->db->order_by($nutrient, 'DESC')->limit('250')->get()->result();
        
        return $fd_list;
    }

    function getCategoryList()
    {
        $gp_list = $this->db->select('id, name')->from($this->nfgTable)->order_by('name', 'ASC')->get()->result();

        $group_list = array('' =>'');
        foreach ($gp_list as $list) 
        {
          $group_list[$list->id] = $list->name;
        }

        return $group_list;
    }

    function getNutrientsList($type)
    {
        $nutrients = array();

        if($type == "vitamins")
        {
            $nutrients['Vit_C_mg'] = 'Vitamina C';
            $nutrients['Vit_B6_mg'] = 'Vitamina B6';
            $nutrients['Vit_B12_ug'] = 'Vitamina B12';
            $nutrients['Vit_A_RAE'] = 'Vitamina A';
            $nutrients['Vit_E_mg'] = 'Vitamina E';
            $nutrients['Vit_D_ug'] = 'Vitamina D';
            $nutrients['Vit_K_ug'] = 'Vitamina K';
            $nutrients['Thiamin_mg'] = 'Vitamina Tiamina';
            $nutrients['Riboflavin_mg'] = 'Vitamina Riboflavina';
            $nutrients['Panto_Acid_mg'] = 'Vitamina Ácido Pantoténico';
            $nutrients['Niacin_mg'] = 'Vitamina Niacina';
        }
        else if($type == "minerals")
        {
            $nutrients['Calcium_mg'] = 'Calcio';
            $nutrients['Iron_mg'] = 'Hierro';
            $nutrients['Magnesium_mg'] = 'Magnesio';
            $nutrients['Phosphorus_mg'] = 'Fósforo';
            $nutrients['Potassium_mg'] = 'Potasio';
            $nutrients['Sodium_mg'] = 'Sodio';
            $nutrients['Zinc_mg'] = 'Zinc';
            $nutrients['Copper_mg'] = 'Cobre';
            $nutrients['Manganese_mg'] = 'Manganeso';
            $nutrients['Selenium_ug'] = 'Selenio';
        }
        else
        {

        }

        return $nutrients;
    }

    function getServings($food_id)
    {
        $serv_list = $this->db->select('id, GmWt_1, GmWt_Desc1, GmWt_2, GmWt_Desc2')->from($this->nfTable)->order_by('Shrt_Desc', 'ASC')->where('id =', $food_id)->get()->result();
        $servings_list = array('' =>'');
        foreach ($serv_list as $list) 
        {
          $servings_list[$food_id][1] = 'gramos';
          if($list->GmWt_1 != null) $servings_list[$list->id][$list->GmWt_1] = $list->GmWt_Desc1;
          if($list->GmWt_2 != null) $servings_list[$list->id][$list->GmWt_2] = $list->GmWt_Desc2;

        }
        return $this->servings_list;
    }

   function getFood($id)
    {
        $result = $this->db->from($this->nfTable)->where('id =', $id)->get()->row();

        return $result;
    }

    function getFoodMacros($id)
    {
        $this->db->select('protein, fat, carbs, fiber, qtty, energy');
        $this->db->from($this->nfTable);
        return $this->db->where('id =', $id)->get()->row();
    }

    function registerFood($params)
    {
        return $this->db->insert($this->nfTable, $params);
    }

    function updateFood($id, $params)
    {
        return $this->db->where('id', $id)->update($this->nfTable, $params);
    }

    function deleteFood($id)
    {
        $this->db->delete($this->nfTable, array('id' => $id));
    }
//////////////////////////////////////////////////////////////

    function translate_DB()
    {
        // 1º frases
        $this->db->query("UPDATE nutrition_foods SET brand = REPLACE(brand, 'Burger King Corporation', 'Burger King') WHERE brand LIKE '%Burger King Corporation%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'dry powder', 'en polvo') WHERE Shrt_Desc LIKE '%dry powder%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'all commercial varieties', 'todas variedades comerciales') WHERE Shrt_Desc LIKE '%all commercial varieties%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'Egg custard', 'Flan de Huevo') WHERE Shrt_Desc LIKE '%Egg custard%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'blood sausage', 'Morcilla') WHERE Shrt_Desc LIKE '%blood sausage%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'hard type', 'curado') WHERE Shrt_Desc LIKE '%hard type%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'semisoft type', 'semi curado') WHERE Shrt_Desc LIKE '%semisoft type%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'soft type', 'tierno') WHERE Shrt_Desc LIKE '%tierno%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'hi-fat', 'sin desgrasar') WHERE Shrt_Desc LIKE '%hi-grasa%';");
        // 2º palabras plurales
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'Avocados', 'Aguacates') WHERE Shrt_Desc LIKE '%Avocados%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'Lemons', 'Limones') WHERE Shrt_Desc LIKE '%Lemons%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'Beans', 'Judías') WHERE Shrt_Desc LIKE '%Beans%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'Leeks', 'Puerros') WHERE Shrt_Desc LIKE '%Leeks%';"); 
        // 3º palabras singular
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'plain', 'entero') WHERE Shrt_Desc LIKE '%plain%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'breakfast', 'desayuno') WHERE Shrt_Desc LIKE '%breakfast%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'powder', 'polvo') WHERE Shrt_Desc LIKE '%powder%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'water', 'agua') WHERE Shrt_Desc LIKE '%water%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'white', 'blanco') WHERE Shrt_Desc LIKE '%white%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'wine', 'vino') WHERE Shrt_Desc LIKE '%wine%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, ', green,', ', verde,') WHERE Shrt_Desc LIKE '%, green,%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, ', red,', ', rojo,') WHERE Shrt_Desc LIKE '%, red,%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, ', chopped,', ', trozeado,') WHERE Shrt_Desc LIKE '%, chopped,%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'Lemonade', 'Limonada') WHERE Shrt_Desc LIKE '%Lemonade%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'Lemon', 'Limón') WHERE Shrt_Desc LIKE '%Lemon%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'Lime', 'Lima') WHERE Shrt_Desc LIKE '%Lime%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'peeled', 'pelado') WHERE Shrt_Desc LIKE '%peeled%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'peel', 'piel') WHERE Shrt_Desc LIKE '%peel%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'juice', 'zumo') WHERE Shrt_Desc LIKE '%juice%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'processed', 'procesado') WHERE Shrt_Desc LIKE '%processed%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'coffee', 'café') WHERE Shrt_Desc LIKE '%coffee%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'cocoa', 'cacao') WHERE Shrt_Desc LIKE '%cocoa%';");
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'coffeecake', 'pastel de café') WHERE Shrt_Desc LIKE '%coffeecake%';");   
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'sardine', 'Sardina') WHERE Shrt_Desc LIKE '%sardine%';");   
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'Atlantic', 'Atlantico') WHERE Shrt_Desc LIKE '%Atlantic%';");  
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'Sall', 'Sal') WHERE Shrt_Desc LIKE '%Sall%';"); 
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'drained', 'escurrido') WHERE Shrt_Desc LIKE '%drained%';"); 
        $this->db->query("UPDATE nutrition_foods SET Shrt_Desc = REPLACE(Shrt_Desc, 'drain', 'escurrido') WHERE Shrt_Desc LIKE '%drain%';"); 

    }



//////////////////////////////////////////
//  SECTION: LOG
/////////////////////////////////////////

    function getFoodNutrientStats($food)
    {
        $nutrients = array(
                //Vitamins
                'Vit_C_mg' => 0, 'Vit_B6_mg' => 0, 'Vit_B12_ug' => 0, 'Vit_A_RAE' => 0, 'Vit_E_mg' => 0, 'Vit_D_ug' => 0, 'Vit_K_ug' => 0, 'Thiamin_mg' => 0, 'Riboflavin_mg' => 0, 'Panto_Acid_mg' => 0, 'Niacin_mg' => 0,
                //Minerals
                'Calcium_mg' => 0, 'Iron_mg' => 0, 'Magnesium_mg' => 0, 'Phosphorus_mg' => 0, 'Potassium_mg' => 0, 'Sodium_mg' => 0, 'Zinc_mg' => 0, 'Copper_mg' => 0, 'Manganese_mg' => 0, 'Selenium_ug' => 0
                );
        if($food)
        {
            //vitamins
            $nutrients['Vit_C_mg'] = $food->Vit_C_mg;
            $nutrients['Vit_B6_mg'] = $food->Vit_B6_mg;
            $nutrients['Vit_B12_ug'] = $food->Vit_B12_ug;
            $nutrients['Vit_A_RAE'] = $food->Vit_A_RAE;
            $nutrients['Vit_E_mg'] = $food->Vit_E_mg;
            $nutrients['Vit_D_ug'] = $food->Vit_D_ug;
            $nutrients['Vit_K_ug'] = $food->Vit_K_ug;
            $nutrients['Thiamin_mg'] = $food->Thiamin_mg;
            $nutrients['Riboflavin_mg'] = $food->Riboflavin_mg;
            $nutrients['Panto_Acid_mg'] = $food->Panto_Acid_mg;
            $nutrients['Niacin_mg'] = $food->Niacin_mg;

            //minerals
            $nutrients['Calcium_mg'] = $food->Calcium_mg;
            $nutrients['Iron_mg'] = $food->Iron_mg;
            $nutrients['Magnesium_mg'] = $food->Magnesium_mg;
            $nutrients['Phosphorus_mg'] = $food->Phosphorus_mg;
            $nutrients['Potassium_mg'] = $food->Potassium_mg;
            $nutrients['Sodium_mg'] = $food->Sodium_mg;
            $nutrients['Zinc_mg'] = $food->Zinc_mg;
            $nutrients['Copper_mg'] = $food->Copper_mg;
            $nutrients['Manganese_mg'] = $food->Manganese_mg;
            $nutrients['Selenium_ug'] = $food->Selenium_ug;

            return $nutrients;
        }
        else
            return false;
    }

    function getNutrientStats($date, $period)
    {
        $nutrients = array(
                //total
                'energy' => 0, 'qtty'=> 0,
                // macros kcal
                'carbs' => 0, 'proteins' => 0, 'fats'=> 0, 
                //macros grams
                'gr_carbs' => 0, 'gr_proteins' => 0,  'gr_fats'=> 0, 'gr_fiber'=> 0, 
                //blocks
                'fat_blocks' => 0, 'protein_blocks' => 0, 'carb_blocks' => 0 , 'total_blocks' => 0, 
                //Vitamins
                'Vit_C_mg' => 0, 'Vit_B6_mg' => 0, 'Vit_B12_ug' => 0, 'Vit_A_RAE' => 0, 'Vit_E_mg' => 0, 'Vit_D_ug' => 0, 'Vit_K_ug' => 0, 'Thiamin_mg' => 0, 'Riboflavin_mg' => 0, 'Panto_Acid_mg' => 0, 'Niacin_mg' => 0,
                //Minerals
                'Calcium_mg' => 0, 'Iron_mg' => 0, 'Magnesium_mg' => 0, 'Phosphorus_mg' => 0, 'Potassium_mg' => 0, 'Sodium_mg' => 0, 'Zinc_mg' => 0, 'Copper_mg' => 0, 'Manganese_mg' => 0, 'Selenium_ug' => 0
                ); 

        $to = $date;  
        if($period == 'day') $from = $to; 
        else if($period == 'week')  $from = date("Y-m-d", strtotime('monday this week')); 
        else if($period == 'month') $from = date('Y-m-01'); 
        else if($period == '3 months') $from = date('Y-m-d', strtotime("-3 months", strtotime($to)));
        else if($period == '6 months') $from = date('Y-m-d', strtotime("-6 months", strtotime($to))); 
        else if($period == '12 months') $from = date('Y-m-d', strtotime("-6 months", strtotime($to))); 

        $result = $this->db->select('nutrition_foods.`from` AS `from`,
                                    nutrition_foods.Fiber_TD_g AS fiber, 
                                    IF((select `from`) LIKE "%USDA_R28%", round((`nutrition_foods`.`Carbohydrt_g`- (select fiber)), 2), round(`nutrition_foods`.`Carbohydrt_g`, 2)) AS carbs,
                                    nutrition_foods.Protein_g AS proteins, 
                                    nutrition_foods.Lipid_Tot_g AS fats, 
                                    nutrition_log.qtty AS qtty,
                                    nutrition_log.food_id AS food_id,

                                    nutrition_foods.Vit_C_mg,
                                    nutrition_foods.Vit_B6_mg,
                                    nutrition_foods.Vit_B12_ug,
                                    nutrition_foods.Vit_A_RAE,
                                    nutrition_foods.Vit_E_mg,
                                    nutrition_foods.Vit_D_ug,
                                    nutrition_foods.Vit_K_ug,
                                    nutrition_foods.Thiamin_mg,
                                    nutrition_foods.Riboflavin_mg,
                                    nutrition_foods.Panto_Acid_mg,
                                    nutrition_foods.Niacin_mg,

                                    nutrition_foods.Calcium_mg,
                                    nutrition_foods.Iron_mg,
                                    nutrition_foods.Magnesium_mg,
                                    nutrition_foods.Phosphorus_mg,
                                    nutrition_foods.Potassium_mg,
                                    nutrition_foods.Sodium_mg,
                                    nutrition_foods.Zinc_mg,
                                    nutrition_foods.Copper_mg,
                                    nutrition_foods.Manganese_mg,
                                    nutrition_foods.Selenium_ug
                                    ', FALSE)
                            ->from($this->nlTable)->where('nutrition_log.user_id =', $this->user_id)
                            ->join($this->nfTable, 'nutrition_log.food_id = nutrition_foods.id')
                            ->join($this->nfgTable, 'nutrition_log.group_id = nutrition_food_groups.id')
                            ->where('nutrition_log.date >=', $from)
                            ->where('nutrition_log.date <=', $to)
                            ->where('nutrition_log.user_id =', $this->user_id)
                            ->get()->result();

        if($result)
        {
            foreach ($result as $res ) {
                //macros grams
                $nutrients['gr_carbs'] += $res->carbs*$res->qtty/100;
                $nutrients['gr_fiber'] += $res->fiber*$res->qtty/100;
                $nutrients['gr_proteins'] += $res->proteins*$res->qtty/100;
                $nutrients['gr_fats'] += $res->fats*$res->qtty/100;
                $nutrients['qtty'] += $res->qtty;

                //vitamins
                $nutrients['Vit_C_mg'] += $res->Vit_C_mg*$res->qtty/100;
                $nutrients['Vit_B6_mg'] += $res->Vit_B6_mg*$res->qtty/100;
                $nutrients['Vit_B12_ug'] += $res->Vit_B12_ug*$res->qtty/100;
                $nutrients['Vit_A_RAE'] += $res->Vit_A_RAE*$res->qtty/100;
                $nutrients['Vit_E_mg'] += $res->Vit_E_mg*$res->qtty/100;
                $nutrients['Vit_D_ug'] += $res->Vit_D_ug*$res->qtty/100;
                $nutrients['Vit_K_ug'] += $res->Vit_K_ug*$res->qtty/100;
                $nutrients['Thiamin_mg'] += $res->Thiamin_mg*$res->qtty/100;
                $nutrients['Riboflavin_mg'] += $res->Riboflavin_mg*$res->qtty/100;
                $nutrients['Panto_Acid_mg'] += $res->Panto_Acid_mg*$res->qtty/100;
                $nutrients['Niacin_mg'] += $res->Niacin_mg*$res->qtty/100;

                //minerals
                $nutrients['Calcium_mg'] += $res->Calcium_mg*$res->qtty/100;
                $nutrients['Iron_mg'] += $res->Iron_mg*$res->qtty/100;
                $nutrients['Magnesium_mg'] += $res->Magnesium_mg*$res->qtty/100;
                $nutrients['Phosphorus_mg'] += $res->Phosphorus_mg*$res->qtty/100;
                $nutrients['Potassium_mg'] += $res->Potassium_mg*$res->qtty/100;
                $nutrients['Sodium_mg'] += $res->Sodium_mg*$res->qtty/100;
                $nutrients['Zinc_mg'] += $res->Zinc_mg*$res->qtty/100;
                $nutrients['Copper_mg'] += $res->Copper_mg*$res->qtty/100;
                $nutrients['Manganese_mg'] += $res->Manganese_mg*$res->qtty/100;
                $nutrients['Selenium_ug'] += $res->Selenium_ug*$res->qtty/100;
            }
            
            //macros kcal
            $nutrients['carbs'] = 4 * $nutrients['gr_carbs'];
            $nutrients['proteins'] = 4 * $nutrients['gr_proteins'];
            $nutrients['fats'] = 9 * $nutrients['gr_fats'];
            $nutrients['energy'] = $nutrients['carbs'] + $nutrients['proteins'] + $nutrients['fats']; 

            //macros blocks
            $blocks = $this->calcBlocks($nutrients['gr_fats'], $nutrients['gr_carbs'], $nutrients['gr_proteins']);
            $nutrients['fat_blocks'] = $blocks['fat'];
            $nutrients['protein_blocks'] = $blocks['protein'];
            $nutrients['carb_blocks'] = $blocks['carb'];
            $nutrients['total_blocks'] = $blocks['total'];

            if($period != 'day') //estadísticas de kcal, gram y bloques por día
            {
                $days_between = round(abs(strtotime($to) - strtotime($from)) / 86400) +1;

                foreach ($nutrients as $key => $value) {
                    $nutrients[$key] = $value / $days_between;
                }
            }
        }

        return $nutrients;
    }

    function getNutrientReq()
    {
        $result = $this->db->select('nutrition_foods_requirements.Vit_C_mg,
                                    nutrition_foods_requirements.Vit_B6_mg,
                                    nutrition_foods_requirements.Vit_B12_ug,
                                    nutrition_foods_requirements.Vit_A_RAE,
                                    nutrition_foods_requirements.Vit_E_mg,
                                    nutrition_foods_requirements.Vit_D_ug,
                                    nutrition_foods_requirements.Vit_K_ug,
                                    nutrition_foods_requirements.Thiamin_mg,
                                    nutrition_foods_requirements.Riboflavin_mg,
                                    nutrition_foods_requirements.Panto_Acid_mg,
                                    nutrition_foods_requirements.Niacin_mg,

                                    nutrition_foods_requirements.Calcium_mg,
                                    nutrition_foods_requirements.Iron_mg,
                                    nutrition_foods_requirements.Magnesium_mg,
                                    nutrition_foods_requirements.Phosphorus_mg,
                                    nutrition_foods_requirements.Potassium_mg,
                                    nutrition_foods_requirements.Sodium_mg,
                                    nutrition_foods_requirements.Zinc_mg,
                                    nutrition_foods_requirements.Copper_mg,
                                    nutrition_foods_requirements.Manganese_mg,
                                    nutrition_foods_requirements.Selenium_ug
                                    ', FALSE)
                            ->from($this->nfrTable)->where('nutrition_foods_requirements.condition =', 'adult >4')
                            ->get()->row();

        return $result;

    }

    function calcDV($requirements, $nutrients)
    {
        $DV = array();
        foreach ($requirements as $key => $value) {
            if($requirements->$key != null && $requirements->$key != 0) $DV[$key] = $nutrients[$key]*100/$requirements->$key;
            else $DV[$key] = 0;
        }

        return $DV;
    }

    function getTimeSeriesMacroStats($date, $period = null)
    {
        $from = $date;
        if($period == null OR $period == 'day' OR $period == 'week') $from = date("Y-m-d", strtotime('monday this week', strtotime($from))); //get this week
        else if($period == 'month') $from = date("Y-m-d", strtotime('-30 day', strtotime($from))); //last 30 days
        else if($period == '3month') $from = date("Y-m-d", strtotime('-90 day', strtotime($from))); //last 90 days
        else if($period == '6month') $from = date("Y-m-d", strtotime('-180 day', strtotime($from))); //last 120 days
        else if($period == '12month') $from = date("Y-m-d", strtotime('-365 day', strtotime($from))); //last 120 days
        
        $results = $this->db->select('nutrition_log.user_id, 
                                      UNIX_TIMESTAMP(nutrition_log.date)*1000 AS date, 
                                    `nutrition_log`.`qtty` AS qtty, 
                                    `nutrition_foods`.`from` AS from,
                                    nutrition_foods.Fiber_TD_g AS fiber, 
                                    SUM(round(`nutrition_foods`.`Energ_Kcal`*qtty/100, 2)) AS energy,
                                    SUM(round(`nutrition_foods`.`Carbohydrt_g`*4*qtty/100, 2)) AS carbs,
                                    SUM(round(`nutrition_foods`.`Protein_g`*4*qtty/100, 2)) AS proteins,
                                    SUM(round(`nutrition_foods`.`Lipid_Tot_g`*9*qtty/100, 2)) AS fats')
                            ->from($this->nlTable)
                            ->join($this->nfTable, 'nutrition_log.food_id = nutrition_foods.id')
                            ->where('nutrition_log.user_id =', $this->user_id)
                            ->where('nutrition_log.date >=', $from)
                            ->group_by('nutrition_log.date')
                            ->get()->result();
        return $results;
    }

    function convert_to_chart($result, $var)
    {
      $converted = '[';
      $i = 1;
      foreach ($result as $res) {
        if($i>1) $coma = ','; else $coma = '';
        $converted .=$coma.'['.$res->date.','.$res->$var.']';
        $i++;
      }
      return $converted.']';
    }
    
    function getLog($date, $days = "1")
    {
        $this->db->select('`nutrition_log`.`id`, 
                          `nutrition_log`.`date`, 
                          `nutrition_log`.`meal`, 
                          `nutrition_log`.`food_id`, 
                          `nutrition_log`.`qtty` AS qtty, 
                          `nutrition_log`.`group_id`,

                          `nutrition_foods`.`from` as `from`,
                          `nutrition_foods`.`Shrt_Desc` as food,  
                          `nutrition_foods`.`brand`, 
                          `nutrition_food_groups`.`name` as `group`, 

                          round(`nutrition_foods`.`Fiber_TD_g`*qtty/100, 2) AS fiber,
                           IF((select `from`) LIKE "%USDA_R28%", round((`nutrition_foods`.`Carbohydrt_g`*qtty/100- (select fiber)), 2), round(`nutrition_foods`.`Carbohydrt_g`*qtty/100, 2)) AS carb,
                          round(`nutrition_foods`.`Protein_g`*qtty/100, 2) AS protein, 
                          round(`nutrition_foods`.`Lipid_Tot_g`*qtty/100, 2) AS fat, 
                          round(`nutrition_foods`.`Energ_Kcal`*qtty/100, 2) AS energy, 
                          round((select fat)/3, 2) AS fat_blocks, 
                          round((select carb)/9, 2) AS carb_blocks, 
                          round((select protein)/7, 2) AS protein_blocks', FALSE)

                ->from($this->nlTable)->where('nutrition_log.user_id =', $this->user_id)
                ->join($this->nfTable, 'nutrition_log.food_id = nutrition_foods.id')
                ->join($this->nfgTable, 'nutrition_log.group_id = nutrition_food_groups.id');

        if($days != null && $days != 1)
        {
            $date = date("Y-m-d", strtotime('-'. $days .' day', strtotime($date)));
            $this->db->where('nutrition_log.date >=', $date);
        }
        else
        {
            $this->db->where('nutrition_log.date =', $date);
        }

        
        return $this->db->order_by('nutrition_log.date', 'DESC')->order_by('id', 'DESC')->get()->result();
    }

    function getLogRegistry($id)
    {
        return $this->db->from($this->nlTable)->where('id =', $id)->get()->row();
    }

    function getLogMacros($id)
    {
        return $this->db->select('protein, fat, carbs, energy')->from($this->nlTable)->where('id =', $id)->get()->row();
    }

    function checkLogUser($id)
    {
        $user_id = $this->db->select('user_id')->from($this->nlTable)->where('id =', $id)->get()->row()->user_id;
        if($user_id == $this->user_id)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function checkExistingRegistry($grp_id, $date, $log, $meal = null)
    {
        $this->db->select('id')->from($log)->where('group_id', $grp_id)->where('date', $date);
        if ($meal != null)  $this->db->where('meal', $meal);
        if($this->db->get()->row())
            return true;
        else
            return false;
    }

    function addLog($params)
    { 
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        foreach ($food_id as $key => $value) 
        {
            $food = $this->getFood($food_id[$key]);
            $params2 = array();
            
            $params2['food_id'] = $food_id[$key];
            $params2['group_id'] = $food->group_id;
            $params2['user_id'] = $user_id;
            $params2['qtty'] = $qtty[$key];
            $params2['date'] = $date;
            $params2['meal'] = $meal;
            // $params2['serving'] = $serving[$key];

            $this->db->insert($this->nlTable, $params2);
        }
    }

    function updateLog($id, $params)
    {
        log_message('debug',print_r($params,TRUE));
        $params['qtty'] = $params['qtty'][0];
        $params['food_id'] = $params['food_id'][0];
        $this->db->where('id', $id)->update($this->nlTable, $params);
    }

    function deleteFoodLog($id)
    {
        $this->db->delete($this->nlTable, array('id' => $id));
    }

}   
?>