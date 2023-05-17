<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Biometrics_lib
{

	public function __construct()
	{
		//$this->booking = & get_instance();
		
		//$this->booking->lang->load('booking');
	}

	function calculate_BMI($weight, $height)
    {
        return  round((float)$weight/(($height/100)^2),2);
    }

    function calcBMI($height, $weight)
    {
        return $weight/($height/100)^2;
    }

    function calculate_Obesity($BMI)
    {
        if ($BMI < 16 ){ $class = "Infrapeso: Delgadez Severa"; 
        }elseif($BMI < 16.99){ $class = "Infrapeso: Delgadez Moderada"; 
        }elseif($BMI < 18.49){ $class = "Infrapeso: Delgadez Aceptable"; 
        }elseif($BMI < 24.99){ $class = "Peso Normal"; 
        }elseif($BMI < 29.99){ $class = "Sobrepeso"; 
        }elseif($BMI < 34.99){ $class = "Obeso: Tipo I"; 
        }elseif($BMI < 40){ $class = "Obeso: Tipo II"; 
        }elseif($BMI >= 40){ $class = "Obeso: Tipo III"; }

        return $class;
    }


}

?>