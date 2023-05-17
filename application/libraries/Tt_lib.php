<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//////////////////////////////
// BASED ON quanticalabs timetable for wp 4.8.1
/////////////////////////////
class Tt_lib
{
	
	public function __construct()
	{
		//$this->booking = & get_instance();
		
		//$this->booking->lang->load('booking');
	}
////////////////////////////////////////
///  CUSTOM FUNCTIONS
////////////////////////////////////////



////////////////////////////////////////
//  WORDPRESS FUNCTIONS
////////////////////////////////////////

	function shortcode_atts( $pairs, $atts, $shortcode = '' ) {
	    $atts = (array)$atts;
	    $out = array();
	    foreach ($pairs as $name => $default) {
	        if ( array_key_exists($name, $atts) )
	            $out[$name] = $atts[$name];
	        else
	            $out[$name] = $default;
	    }
	    /**
	     * Filters a shortcode's default attributes.
	     *
	     * If the third parameter of the shortcode_atts() function is present then this filter is available.
	     * The third parameter, $shortcode, is the name of the shortcode.
	     *
	     * @since 3.6.0
	     * @since 4.4.0 Added the `$shortcode` parameter.
	     *
	     * @param array  $out       The output array of shortcode attributes.
	     * @param array  $pairs     The supported attributes and their defaults.
	     * @param array  $atts      The user defined shortcode attributes.
	     * @param string $shortcode The shortcode name.
	     */
	    if ( $shortcode ) {
	        $out = apply_filters( "shortcode_atts_{$shortcode}", $out, $pairs, $atts, $shortcode );
	    }
	 
	    return $out;
	}

	function shortcode_parse_atts($text) {
	    $atts = array();
	    $pattern = get_shortcode_atts_regex();
	    $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
	    if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
	        foreach ($match as $m) {
	            if (!empty($m[1]))
	                $atts[strtolower($m[1])] = stripcslashes($m[2]);
	            elseif (!empty($m[3]))
	                $atts[strtolower($m[3])] = stripcslashes($m[4]);
	            elseif (!empty($m[5]))
	                $atts[strtolower($m[5])] = stripcslashes($m[6]);
	            elseif (isset($m[7]) && strlen($m[7]))
	                $atts[] = stripcslashes($m[7]);
	            elseif (isset($m[8]) && strlen($m[8]))
	                $atts[] = stripcslashes($m[8]);
	            elseif (isset($m[9]))
	                $atts[] = stripcslashes($m[9]);
	        }
	 
	        // Reject any unclosed HTML elements
	        foreach( $atts as &$value ) {
	            if ( false !== strpos( $value, '<' ) ) {
	                if ( 1 !== preg_match( '/^[^<]*+(?:<[^>]*+>[^<]*+)*+$/', $value ) ) {
	                    $value = '';
	                }
	            }
	        }
	    } else {
	        $atts = ltrim($text);
	    }
	    return $atts;
	}

	function to_decimal_time($time, $midReplace = false)
	{
		$timeExplode = explode(".", $time);
		return ($midReplace && (int)$timeExplode[0]==0 ? 24 : $timeExplode[0]) . "." . (isset($timeExplode[1]) && (int)$timeExplode[1]>0 ? sprintf("%02s", ceil($timeExplode[1]/60*100)) : "00");
	}

	function get_next_row_hour($hour, $measure)
	{
		$hourExplode = explode(".", $hour);
		if((int)$hourExplode[1]>0)
		{
			if((int)$hourExplode[1]+$measure*100>100)
			{
				$hour = (int)$hourExplode[0]+1;
				if($hour==24)
					$hour = 0;
				$minutes = "00";
			}
			else if(fmod((int)$hourExplode[1],(double)$measure*100)!=0)
			{
				for($i=0; $i<100; $i=$i+$measure*100)
				{
					if((int)$hourExplode[1]<$i)
					{
						$minutes = $i;
						break;
					}
				}
				$hour = (int)$hourExplode[0];
			}
			else
			{
				$hour = (int)$hourExplode[0];
				$minutes = (int)$hourExplode[1];
			}
		}
		else
		{
			$hour = (int)$hourExplode[0];
			$minutes = (int)$hourExplode[1];
		}
		if($hour . "." . $minutes == "0.00")
			return "24.00";
		return $hour . "." . $minutes;
	}

	function tt_hour_in_array($hour, $array, $measure, $hours_min)
	{
		$array_count = count($array);
		for($i=0; $i<$array_count; $i++)
		{
			if((int)$measure==1)
			{
				if((!isset($array[$i]["displayed"]) || (bool)$array[$i]["displayed"]!=true) && (int)$array[$i]["start"]==(int)$hour)
					return true;
			}
			else
			{
				if((!isset($array[$i]["displayed"]) || (bool)$array[$i]["displayed"]!=true) && $this->to_decimal_time($this->roundMin($array[$i]["start"], $measure, $hours_min))==(double)$hour)
					return true;
			}
		}
		return false;
	}

	function tt_get_rowspan_value($hour, $array, $rowspan, $measure, $hours_min)
	{
		$array_count = count($array);
		$found = false;
		$hours = array();
		if((int)$measure==1)
		{
			for($i=(int)$hour; $i<(int)$hour+$rowspan; $i++)
				$hours[] = $i;
			for($i=0; $i<$array_count; $i++)
			{
				if(in_array((int)$array[$i]["start"], $hours))
				{
					$end_explode = explode(".", $array[$i]["end"]);
					$end_hour = (int)$array[$i]["end"] + ((int)$end_explode[1]>0 ? 1 : 0);
					if($end_hour-(int)$hour>1 && $end_hour-(int)$hour>$rowspan)
					{
						$rowspan = $end_hour-(int)$hour;
						$found = true;
					}
				}
			}
		}
		else
		{
			for($i=(double)$hour; $i<(double)$hour+$rowspan*$measure; $i=$i+$measure)
				$hours[] = $i;
			for($i=0; $i<$array_count; $i++)
			{
				if(in_array($this->to_decimal_time($this->roundMin($array[$i]["start"], $measure, $hours_min)), $hours))
				{
					$end_hour = $this->to_decimal_time($array[$i]["end"], false); //changed to false - wrong value for ex. 00:30 end hour
					//$end_hour = ($end_hour<24 ? get_next_row_hour($end_hour, $measure) : $end_hour);
					$end_hour = $this->get_next_row_hour($end_hour, $measure);
					if($end_hour-(double)$hour>$measure && ($end_hour-(double)$hour)/$measure>$rowspan)
					{
						$rowspan = ($end_hour-(double)$hour)/$measure;
						$found = true;
					}
				}
			}
		}
		if(!$found)
			return $rowspan;
		else
			return $this->tt_get_rowspan_value($hour, $array, $rowspan, $measure, $hours_min);
	}

	function do_shortcode($var)
	{
		return $var;
	}

	function roundMin($time, $measure, $hours_min)
	{
		/*echo "TIME:" . $time . "<br>";
		echo "HOURS_MIN:" . $hours_min . "<br>";
		$roundTo = $measure*60;
		$seconds = date('U', strtotime($time));
		return date("H.i", floor($seconds / ($roundTo * 60)) * ($roundTo * 60));*/
		
		$decimal_time = $this->to_decimal_time($time);
		$found = false;
		while(!$found)
		{
			$hours_min=$hours_min+$measure;
			if($hours_min>$decimal_time)
				$found = true;
		}
		$hours_min = number_format($hours_min-$measure, 2);
		$hours_min_explode = explode(".", $hours_min);
		return str_pad($hours_min_explode[0], 2, '0', STR_PAD_LEFT) . "." . ((int)$hours_min_explode[1]>0 ? (int)$hours_min_explode[1]*60/100 : "00");
	}


}

?>