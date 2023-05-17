<?php

defined('BASEPATH') OR exit('No direct script access allowed');

define("BOOKING_POPUP_MESSAGE", "<h2>You are about to book event</h2>
<p class='event_details'>{event_title}</p>
<p class='event_details'>{column_title}</p>
<p class='event_details'>{event_start} - {event_end}</p>
<p>An initial receipt will be sent out automatically unless you decide not to do so below.</p>
<div>{tt_btn_book}{tt_btn_cancel}</div>");

define("BOOKING_POPUP_THANK_YOU_MESSAGE", "<h2>Thank you for choosing our services!</h2>
<p class='event_details'>{event_title}</p>
<p class='event_details'>{column_title}</p>
<p class='event_details'>{event_start} - {event_end}</p>
<p class='info'>This is a confirmation of your booking. Your booking is now complete and a confirmation email has been sent to you.</p>
<div>{tt_btn_continue}</div>");



class Timetable extends CI_Controller {

    function __construct() 
    {
        parent::__construct();
        $this->load->database();

        $this->load->library(array('ion_auth','form_validation','booking_lib','tt_lib'));

        $this->load->helper(array('url','language'));

        $this->load->model('box_model', 'box');
        $this->load->model('logs_model', 'logs');
        $this->load->model('booking_model', 'booking');
        $this->load->model('ion_auth_model', 'ion');
        $this->load->model('timetable_model', 'tt');

        $this->lang->load(array('auth','fitbox','booking'));

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $groups = array('sadmin', 'admin', 'fcoach', 'coach', 'finance', 'rrhh', 'comercial', 'marketing', 'athlete');
        if ($this->ion_auth->logged_in() && $this->ion_auth->in_group($groups))
        {
            if($this->box->set_box())
            {
               $this->tt->set_box($this->box->box_id);
               $this->booking->set_box($this->box->box_id); 
            }
            
        }   
    }


    function check_login($groups)
    {
        if (!$this->ion_auth->logged_in() )
        {
            // redirect them to the login page
            redirect('auth/login', 'refresh');
        }
        else if (!$this->ion_auth->in_group($groups))
        {
            // redirect them to the login page
            $group = $this->ion->get_user_group();
            redirect(base_url() . $group, 'refresh');
        }else{
            return true;
        }
        return false;
    }

    function index() 
    {

        if ($this->check_login(array('sadmin','admin') )
        {

            $data['atts'] =  array(
                "event" => $this->tt->get_services(),
                "columns" => "Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo",
                "show_booking_button" => "on_hover",
                "show_available_slots" => "always",
                "event_layout" => 6,
                "row_height" =>20,
                "measure" => '0.5',
                "hide_hours_column" => 1

            );

            $data['weekdays'] = $this->tt->get_weekdays();
            $data['hours'] = $this->tt->get_min_max_hours();
            $data['output'] = $this->tt_timetable($data['atts']);


            $this->input->post('ajax') 
            {  
                $this->load->view('backend/staff/timetable', $data);
            }else{
                $data2['user'] = $this->box->getUser($this->session->userdata('user_id'));
                $this->load->view('backend/staff/partials/blank', $data2);
                $this->load->view('backend/staff/timetable', $data);
                $this->load->view('backend/partials/footer');
            }
        }

    }

    function raw()
    {
        $data['atts'] =  array(
                "event" => $this->tt->get_services(),
                "columns" => "Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo",
                "show_booking_button" => "on_hover",
                "show_available_slots" => "always",
                "event_layout" => 6,
                "row_height" =>20,
                "measure" => '0.5',
                "hide_hours_column" => 1

            );

            $data['weekdays'] = $this->tt->get_weekdays();
            $data['hours'] = $this->tt->get_min_max_hours();
            $data['output'] = $this->tt_timetable($data['atts']);

            $this->load->view('backend/staff/raw', $data);
    }

    function tt_timetable($atts, $content = null)
    {        
        $defaults = array(
            "event" => "",
            "event_category" => "",
            "events_page" => "",
            "filter_style" => "tabs", 
            "filter_kind" => "event",
            "measure" => '1',
            "show_booking_button" => "no", // no, on_hover, always
            "show_available_slots" => "no",
            "booking_label" => "Book now",
            "booked_label" => "Booked",
            "unavailable_label" => "Unavailable",
            "booking_popup_label" => "Book now",
            "login_popup_label" => "Log in",
            "cancel_popup_label" => "Cancel",
            "continue_popup_label" => "Continue",       
            "booking_popup_message" => BOOKING_POPUP_MESSAGE,
            "booking_popup_thank_you_message" => BOOKING_POPUP_THANK_YOU_MESSAGE,
            "filter_label" => "All Events",
            "filter_label_2" => "All Events Categories",
            "hour_category" => "",
            "columns" => "",
            "time_format" => "H:i",
            "hide_hours_column" => 0,
            "hide_all_events_view" => 0,
            "show_end_hour" => 0,
            "event_layout" => 1,
            "box_bg_color" => "00A27C",
            "box_hover_bg_color" => "1F736A",
            "box_txt_color" => "FFFFFF",
            "box_hover_txt_color" => "FFFFFF",
            "box_hours_txt_color" => "FFFFFF",
            "box_hours_hover_txt_color" => "FFFFFF",
            "filter_color" => "00A27C",
            "row1_color" => "F0F0F0",
            "row2_color" => "",
            "booking_text_color" => "FFFFFF",
            "booking_bg_color" => "05BB90",
            "booking_hover_text_color" => "FFFFFF",
            "booking_hover_bg_color" => "07B38A",
            "booked_text_color" => "AAAAAA",
            "booked_bg_color" => "EEEEEE",
            "unavailable_text_color" => "AAAAAA",
            "unavailable_bg_color" => "EEEEEE",
            "available_slots_color" => "FFD544",
            "hide_empty" => 0,
            "disable_event_url" => 0,
            "text_align" => "center",
            "row_height" => 31,
            "id" => "",
            "shortcode_id" => "",
            "responsive" => 1,
            "event_description_responsive" => "none",
            "collapse_event_hours_responsive" => 1,
            "colors_responsive_mode" => 1,
            "export_to_pdf_button" => 1,
            "generate_pdf_label" => "Generate PDF",
            "direction" => "ltr",
            "font_custom" => "",
            "font" => "",
            "font_subset" => "",
            "font_size" => "",
            "custom_css" => ""
        );
        
        if(isset($atts['shortcode_id']) && strlen($atts['shortcode_id']))
        {
            $timetable_shortcodes_list = $this->tt->get_option("timetable_shortcodes_list");
            if($timetable_shortcodes_list!==false && !empty($timetable_shortcodes_list[$atts['shortcode_id']]))
            {
                $shortcode = html_entity_decode(str_replace(array("[", "]"), "", $timetable_shortcodes_list[$atts['shortcode_id']]));
                $shortcode_atts = $this->tt_lib->shortcode_parse_atts($shortcode);
                $defaults = array_merge($defaults, $shortcode_atts);
            }
        }
        
        $atts = $atts2 = array_merge($defaults, $atts);
        extract($atts);
        $measure = (double)$measure;

        //replace grave accent added by Visual Composer
        $atts2["booking_popup_message"] = $booking_popup_message = str_replace("``", "\"", $booking_popup_message);
        $atts2["booking_popup_thank_you_message"] = $booking_popup_thank_you_message = str_replace("``", "\"", $booking_popup_thank_you_message);
        $custom_css = str_replace("``", "\"", $custom_css);

        $color_params = array('box_bg_color','box_hover_bg_color','box_txt_color','box_hover_txt_color','box_hours_txt_color','box_hours_hover_txt_color','filter_color','row1_color','row2_color','booking_text_color','booking_bg_color','booking_hover_text_color','booking_hover_bg_color','booked_text_color','booked_bg_color','unavailable_text_color','unavailable_bg_color','available_slots_color');

        foreach($color_params as $color_param)
        {
            if(!empty($$color_param)) $$color_param = ltrim($$color_param, "#");
        }

        //////////////////////////
        /////// CABECERA FILTRO
        //////////////////////////
        $events_array = array_values(array_diff(array_filter(array_map('trim', explode(",", $event))), array("-")));
        $event_category_array = array_values(array_diff(array_filter(array_map('trim', explode(",", $event_category))), array("-")));

                
        if(!$hide_all_events_view)
        {
            $events_list_html = '<li><a href="#all-events' . ($id!='' ? '-' . urlencode($id) : '') . '" title="' . $filter_label . '">' . $filter_label . '</a></li>';
            $events_categories_list_html = '<li><a href="#all-events' . ($id!='' ? '-' . urlencode($id) : '') . '" title="' . ($filter_kind=="event_and_event_category" ? $filter_label_2 : $filter_label) . '">' . ($filter_kind=="event_and_event_category" ? $filter_label_2 : $filter_label) . '</a></li>';
        }
        else
        {
            $events_list_html = '';
            $events_categories_list_html = '';
        }

        if($filter_kind=="event" OR !count($event_category_array) OR ($filter_kind=="event_and_event_category" && !empty($event)))
        {
            $events_array_count = count($events_array);
            for($i=0; $i<$events_array_count; $i++)
            {

                $events_list_html .= '<li><a href="#' . urlencode($events_array[$i]) . '" title="' . $events_array[$i] . '">' . $events_array[$i] . '</a></li>';
                if($hide_all_events_view && $filter_style=="dropdown_list" && ($filter_label=="All Events" OR $filter_label=="") && !$i)
                {
                    $filter_label = $events_list[$i]->post_title;
                }
            }
        }

        $events_category_array_count = 0;
        if($filter_kind=="event_category" OR ($filter_kind=="event_and_event_category" && !empty($event_category)))
        {
            $events_category_array_count = count($event_category_array);
            for($i=0; $i<$events_category_array_count; $i++)
            {
                $category = ''; //get_term_by("slug", $event_category_array[$i], "events_category");
                if(!empty($category))
                {
                    $events_categories_list_html .= '<li><a href="#' . urlencode($event_category_array[$i]) . '" title="' . $category->name . '">' . $category->name . '</a></li>';
                    if($hide_all_events_view && $filter_style=="dropdown_list" && !$i)
                    {
                        if($filter_kind!="event_and_event_category" && ($filter_label=="All Events" OR $filter_label==""))
                            $filter_label = $category->name;
                        if($filter_kind=="event_and_event_category" && ($filter_label_2=="All Events Categories" OR $filter_label_2==""))
                            $filter_label_2 = $category->name;
                    }
                }
            }
        }     
                
                $events_array_verified = array();
                // if(count($event_category_array))
                // {
                //     //events array ids
                //     $events_array_id = array();
                //     for($i=0; $i<count($events_array); $i++)
                //     {
                //         $event_post = get_posts(array(
                //           'name' => $events_array[$i],
                //           'post_type' => $timetable_events_settings['slug'],
                //           'post_status' => 'publish',
                //           'numberposts' => 1
                //         ));
                //         $events_array_id[] = $event_post[0]->ID;
                //     }
                //     $events_array_cat = get_posts(array(
                //         'include' => $events_array_id,
                //         'post_type' => $timetable_events_settings['slug'],
                //         'post_status' => 'publish',
                //         'posts_per_page' => -1,
                //         'nopaging' => true,
                //         'orderby' => 'menu_order',
                //         'order' => 'ASC',
                //         'events_category' => implode("','", array_map("tt_strtolower_urlencode", $event_category_array))
                //     ));
                //     if(!empty($events_array_cat))
                //     {       
                        
                //         for($i=0; $i<count($events_array_cat); $i++)
                //             $events_array_verified[] = urldecode($events_array_cat[$i]->post_name);
                //     }
                //     else
                //         $events_array_verified = -1;
                // }
         
         $output = '';
        $output .= "<div class='tt_wrapper " . ($direction=="rtl" ? "rtl" : "") . "'>";

        if($filter_style=="dropdown_list")
        {
            $output .= '<div class="tt_navigation_wrapper ' . ($filter_kind=="event_and_event_category" ? "tt_double_buttons" : "") . '">
            <div class="tt_navigation_cell timetable_clearfix">';
            
            if($filter_kind=="event_category" OR $filter_kind=="event_and_event_category")
            {
                $output .= '<ul class="timetable_clearfix tabs_box_navigation events_categories_filter' . ((int)$responsive ? " tt_responsive" : "") . ' sf-timetable-menu' . ($id!="" ? ' ' . urlencode($id) : '') . '">
                    <li class="tabs_box_navigation_selected" aria-haspopup="true"><label>' . ($filter_kind=="event_and_event_category" ? $filter_label_2 : $filter_label) . '</label><span class="tabs_box_navigation_icon"></span>' . (!$hide_all_events_view OR !empty($event_category) ? '<ul class="sub-menu">' . $events_categories_list_html . '</ul>' : '') . '</li>
                </ul>';
            }
            
            if($filter_kind=="event" OR $filter_kind=="event_and_event_category")
            {
                $output .= '<ul class="timetable_clearfix tabs_box_navigation events_filter' . ((int)$responsive ? " tt_responsive" : "") . ' sf-timetable-menu' . ($id!="" ? ' ' . urlencode($id) : '') . '">
                    <li class="tabs_box_navigation_selected" aria-haspopup="true"><label>' . $filter_label . '</label><span class="tabs_box_navigation_icon"></span>' . (!$hide_all_events_view OR !empty($event) ? '<ul class="sub-menu">' . $events_list_html . '</ul>' : '') . '</li>
                </ul>';
            }
            
            $output .= '</div>';
            
            if($export_to_pdf_button && $responsive)
            {
                $output .= '
                    <div class="tt_navigation_cell timetable_clearfix">
                        <form class="tt_generate_pdf" action="" method="post">
                            <textarea class="tt_pdf_html" name="tt_pdf_html_content"></textarea>
                            <input type="hidden" name="tt_action" value="tt_generate_pdf"/>
                            <input type="submit" value="' . $generate_pdf_label . '"/>
                        </form>
                    </div>';
            }

            $output .= '</div>';
        }


        if((int)$row_height!=31 OR strtoupper($box_bg_color)!="00A27C" OR strtoupper($filter_color)!="00A27C" OR $custom_css!="")
        {
            $output .= '<style type="text/css">' . $custom_css . ((int)$row_height!=31 ? ($id!="" ? '#' . $id : '') . '.tt_tabs .tt_timetable td{height: ' . (int)$row_height . (substr($row_height, -2)!="px" ? 'px' : '') . ';}' : '') . (strtoupper($box_bg_color)!="00A27C" ? ($id!="" ? '#' . $id : '') . '.tt_tabs .tt_timetable .event{background: #' . $box_bg_color . ';}' : '') . (strtoupper($filter_color)!="00A27C" ? ($id!="" ? '#' . $id : '') . ' .tt_tabs_navigation li a:hover,' . ($id!="" ? '#' . $id : '') . ' .tt_tabs_navigation li a.selected,' . ($id!="" ? '#' . $id : '') . ' .tt_tabs_navigation li.ui-tabs-active a{border-color:#' . $filter_color . ' !important;}' . ($id!="" ? '.' . $id : '') . '.tabs_box_navigation.sf-timetable-menu .tabs_box_navigation_selected{background-color:#' . $filter_color . ';border-color:#' . $filter_color . ';}' . ($id!="" ? '.' . $id : '') . '.tabs_box_navigation.sf-timetable-menu .tabs_box_navigation_selected:hover{background-color: #FFF; border: 1px solid rgba(0, 0, 0, 0.1);}' . ($id!="" ? '.' . $id : '') . '.sf-timetable-menu li ul li a:hover, .sf-timetable-menu li ul li.selected a:hover{background-color:#' . $filter_color . ';}' : '') . '</style>';
        }

        if($font!="") $output .= '<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=' . $font . '&amp;subset=' . $font_subset . '">';

        if($font_custom!="" OR $font!="" OR (int)$font_size>0)
        {
            $font_explode = explode(":", $font);
                $font = '"' . $font_explode[0] . '"';
            $output .= '<style type="text/css">' . ($font_custom!="" OR $font!="" ? ($id!="" ? '#' . $id : '') . '.tt_tabs .tt_timetable, #tt_booking_popup_message .tt_booking_message, #tt_booking_popup_message h2{font-family:' . ($font_custom!="" ? $font_custom : $font) . ' !important;}' : '') . ((int)$font_size>0 ? ($id!="" ? '#' . $id : '') . '.tt_tabs .tt_timetable th,' . ($id!="" ? '#' . $id : '') . '.tt_tabs .tt_timetable td,' . ($id!="" ? '#' . $id : '') . '.tt_tabs .tt_timetable .event .before_hour_text,' . ($id!="" ? '#' . $id : '') . '.tt_tabs .tt_timetable .event .after_hour_text,' . ($id!="" ? '#' . $id : '') . '.tt_tabs .tt_timetable .event .event_header{font-size:' . (int)$font_size . 'px !important;}' : '') . '</style>';
        }
        $output .= '<div class="timetable_clearfix tt_tabs' . ((int)$responsive ? " tt_responsive" : "") . " event_layout_" . $event_layout . '"' . ($id!="" ? ' id="' . $id . '"' : '') . '>';

        $output .= '<div class="tt_navigation_wrapper ' . ($filter_style=='tabs' ? '' : 'tt_hide') . '">
            <div class="tt_navigation_cell timetable_clearfix">';

        // we need to display all filter items, both events and events categories, so the filter buttons from both lists are working correctly
        if($filter_kind=="event_and_event_category")
        {
            $all_filters_list_html = $events_categories_list_html . $events_list_html;
            // filter list must be hidden
            $output .= '<ul class="timetable_clearfix tt_tabs_navigation all_filters" style="display: none !important;">' . $all_filters_list_html . '</ul>';
        }

        if($filter_kind=="event_category" OR $filter_kind=="event_and_event_category")
        {
            $events_categories_list_html_view_all = '';
            if($hide_all_events_view && empty($event_category))
                $events_categories_list_html_view_all = '<li><a href="#all-events' . ($id!='' ? '-' . urlencode($id) : '') . '" title="' . ($filter_kind=="event_and_event_category" ? $filter_label_2 : $filter_label) . '">' . ($filter_kind=="event_and_event_category" ? $filter_label_2 : $filter_label) . '</a></li>';
            
            $output .= '<ul class="timetable_clearfix tt_tabs_navigation events_categories_filter"' . ($filter_style=="dropdown_list" ? ' style="display: none;"' : '') . '>' .  $events_categories_list_html_view_all . $events_categories_list_html . '</ul>';
        }
        if($filter_kind=="event" OR $filter_kind=="event_and_event_category")
        {
            $events_list_html_view_all = '';
            if($hide_all_events_view && empty($event))
                $events_list_html_view_all = '<li><a href="#all-events' . ($id!='' ? '-' . urlencode($id) : '') . '" title="' . $filter_label . '">' . $filter_label . '</a></li>';
            
            $output .= '<ul class="timetable_clearfix tt_tabs_navigation events_filter"' . ($filter_style=="dropdown_list" ? ' style="display: none;"' : '') . '>' . $events_list_html_view_all . $events_list_html . '</ul>';
        }

        $output .= '</div>';

        if($export_to_pdf_button && $filter_style=='tabs' && $responsive)
        {
            $output .= '
                <div class="tt_navigation_cell timetable_clearfix">
                    <form class="tt_generate_pdf" action="" method="post">
                        <textarea class="tt_pdf_html" name="tt_pdf_html_content"></textarea>
                        <input type="hidden" name="tt_action" value="tt_generate_pdf"/>
                        <input type="submit" value="' . $generate_pdf_label . '"/>
                    </form>
                </div>';
        }

        $output .= '</div>';

 //// COMENZABA TT

        if(!$hide_all_events_view)
        {
            $output .= '<div id="all-events' . ($id!='' ? '-' . urlencode($id) : '') . '">' . (empty($events_array_verified) ? $this->tt_get_timetable($atts, $events_array) : ($events_array_verified!=-1 ? $this->tt_get_timetable($atts, $events_array_verified) : "No hay eventos disponibles")) . '</div>';     
        }

        // if($filter_kind=="event" OR !count($event_category_array) OR $filter_kind=="event_and_event_category")
        // {
        //     for($i=0; $i<$events_array_count; $i++)
        //     {           
        //         $post = get_page_by_path($events_array[$i], ARRAY_A, $timetable_events_settings['slug']);                   
        //         $categories = wp_get_post_terms($post["ID"], "events_category");
        //         $categories_str = "";
        //         foreach($categories as $category)
        //             $categories_str .= "tt-event-category-" . $category->slug . " ";
        //         $output .= '<div id="' . urlencode($events_array[$i]) . '" class="tt-ui-tabs-hide ' . $categories_str . '">' . (empty($events_array_verified) OR ($events_array_verified!=-1 && in_array($events_array[$i], $events_array_verified)) ? $this->tt_get_timetable($atts, $events_array[$i]) : echo("No hay eventos disponibles")) . '</div>';          
        //     }
        // }
        // if($filter_kind=="event_category" OR $filter_kind=="event_and_event_category")
        // {
        //     for($i=0; $i<$events_category_array_count; $i++)
        //     {
        //         $events_array_posts = array();
        //         $events_array_posts = get_posts(array(
        //             'include' => (array)$events_array_id,
        //             'post_type' => $timetable_events_settings['slug'],
        //             'post_status' => 'publish',
        //             'events_category' => $event_category_array[$i],
        //             'posts_per_page' => -1,
        //             'nopaging' => true
        //         ));
        //         $events_array_for_timetable = array();
        //         for($j=0; $j<count($events_array_posts); $j++)
        //             $events_array_for_timetable[] = urldecode($events_array_posts[$j]->post_name);
        //         $output .= '<div id="' . urlencode($event_category_array[$i]) . '" class="tt-ui-tabs-hide">' . (count($events_array_posts) ? $this->tt_get_timetable($atts, $events_array_for_timetable) : sprintf(__('No %1$s available in %2$s category!', 'timetable'), strtolower($timetable_events_settings['label_plural']), $event_category_array[$i])) . '</div>';         
        //     }
        // }


        $output .= '</div>';
        
        $output .= "<div id='tt_error_message' class='tt_hide'>" . sprintf('No events available error') . "</div>";
        
        $output .= "<div id='tt_booking_popup_message' class='tt_hide'>
            <div class='tt_booking_message_wrapper'>
                <div class='tt_booking_message' data-event-hour-id>
                </div>
                <div class='tt_preloader tt_hide'>
                    <div class='bounce1'></div>
                    <div class='bounce2'></div>
                    <div class='bounce3'></div>
                </div>
            </div>
            
        </div>";
        
        $output .= '<input type="hidden" class="timetable_atts" name="timetable_atts" value="' . htmlentities(json_encode($atts2)) . '" />';
        
        $output .= "</div>";
        
        return $output;
    }

    /**
     * Generates the Timetable HTML code
     * 
     * @param type $atts - timetable options
     * @param type $event - events that will be displayed
     * @return string - Timetable HTML code
     */
    function tt_get_timetable($atts, $event = null)
    {
        
        extract($this->tt_lib->shortcode_atts(array(
            "events_page" => "",
            "measure" => 1,
            "filter_style" => "dropdown_list",
            "filter_label" => "All Events",
            "show_booking_button" => "no",
            "show_available_slots" => "no",
            "booking_label" => "Book now",
            "booked_label" => "Booked",
            "unavailable_label" => "Unavailable",
            "hour_category" => "",
            "columns" => "",
            "time_format" => "H.i",
            "hide_hours_column" => 0,
            "show_end_hour" => 0,
            "event_layout" => 1,
            "box_bg_color" => "00A27C",
            "box_hover_bg_color" => "1F736A",
            "box_txt_color" => "FFFFFF",
            "box_hover_txt_color" => "FFFFFF",
            "box_hours_txt_color" => "FFFFFF",
            "box_hours_hover_txt_color" => "FFFFFF",
            "row1_color" => "F0F0F0",
            "row2_color" => "",
            "booking_text_color" => "FFFFFF",
            "booking_bg_color" => "05BB90",
            "booking_hover_text_color" => "FFFFFF",
            "booking_hover_bg_color" => "07B38A",
            "booked_text_color" => "AAAAAA",
            "booked_bg_color" => "EEEEEE",
            "unavailable_text_color" => "AAAAAA",
            "unavailable_bg_color" => "EEEEEE",
            "available_slots_color" => "FFD544",
            "hide_empty" => 0,
            "disable_event_url" => 0,
            "text_align" => "center",
            "row_height" => 31,
            "id" => "",
            "responsive" => 1,
            "event_description_responsive" => "none",
            "collapse_event_hours_responsive" => 0,
            "colors_responsive_mode" => 0,
        ), $atts));
        //remove leading '#' hash character
        $color_params = array('box_bg_color','box_hover_bg_color','box_txt_color','box_hover_txt_color','box_hours_txt_color','box_hours_hover_txt_color','filter_color','row1_color','row2_color','booking_text_color','booking_bg_color','booking_hover_text_color','booking_hover_bg_color','booked_text_color','booked_bg_color','unavailable_text_color','unavailable_bg_color','available_slots_color');
        foreach($color_params as $color_param)
        {
            if(!empty($$color_param)) $$color_param = ltrim($$color_param, "#");
        }
        $measure = (double)$measure;
        
        $user_id = $this->session->userdata('user_id');
        
        $weekdays_in_query = "";
        if($columns!="")
        {
            $weekdays_explode = explode(",", $columns);
            
            foreach($weekdays_explode as $weekday_explode)
                $weekdays_in_query .= "'" . strtolower(urlencode($weekday_explode)) . "'" . ($weekday_explode!=end($weekdays_explode) ? "," : "");
        }
        if($hour_category!=null && $hour_category!="-")
            $hour_category = array_values(array_diff(array_filter(array_map('trim', explode(",", $hour_category))), array("-")));
        $output = "";
        
        $event_hours = $this->tt->get_activities($event);

        if(!count($event_hours))  return sprintf("No events available hours");

        //print("<pre>".print_r($event_hours,true)."</pre>");

        $event_hours_tt = array();
        foreach($event_hours as $event_hour)
        {
            //$event_hours_tt[($event_hour->menu_order>1 ? $event_hour->menu_order-1 : 7)][] = array(
            $event_hours_tt[$event_hour->menu_order][] = array(
                "start" => $event_hour->start,
                "end" => $event_hour->end,
                "tooltip" => $event_hour->tooltip,
                "before_hour_text" => $event_hour->before_hour_text,
                "after_hour_text" => $event_hour->after_hour_text,
                "available_places" => $event_hour->available_places,
                "booking_count" => $event_hour->booking_count,
                "current_user_booking_id" => $event_hour->current_user_booking_id,
                "event_hours_id" => $event_hour->event_hours_id,
                "tooltip" => $event_hour->tooltip,
                "id" => $event_hour->event_id,
                "title" => $event_hour->event_title,
                "name" => $event_hour->post_name,
                "color_bg" => $event_hour->color_bg,
                "color_hover" => $event_hour->color_hover,

            );
        }
        
        $output .= '<table class="tt_timetable">
                    <thead>
                        <tr class="row_gray"' . ($row1_color!="" ? ' style="background-color: ' . ($row1_color!="transparent" ? '#' : '') . $row1_color . ' !important;"' : '') . '>';
                        if(!(int)$hide_hours_column)
                            $output .= '<th></th>';
        //get weekdays
        $weekdays = $this->tt->get_weekdays();
        foreach($weekdays as $weekday)
        {
            $output .= '    <th>' . $weekday->title . '</th>';
        }
        $output .= '    </tr>
                    </thead>
                    <tbody>';
        //get min anx max hour
        $hours = $this->tt->get_min_max_hours();
        $drop_columns = array();   $l = 0;   $increment = 1;
        $hours_min = (int)$hours->min;
        if((int)$measure==1)
        {
            $max_explode = explode(".", $hours->max);
            $max_hour = (int)$hours->max + (!empty($max_explode[1]) && (int)$max_explode[1]>0 ? 1 : 0);
        }
        else
        {
            $max_hour = $hours->max;
            $max_hour = $this->tt_lib->to_decimal_time($max_hour);
            $max_hour = $this->tt_lib->get_next_row_hour($max_hour, $measure);
            $increment = (double)$measure;
            $hours_min = $this->tt_lib->to_decimal_time($this->tt_lib->roundMin($hours->min, $measure, $this->tt_lib->to_decimal_time($hours_min)));
        }
        for($i=$hours_min; $i<$max_hour; $i=$i+$increment)
        {
            if((int)$measure==1)
            {
                $start = str_pad($i, 2, '0', STR_PAD_LEFT) . '.00';
                $end = str_replace("24", "00", str_pad($i+1, 2, '0', STR_PAD_LEFT)) . '.00';
            }
            else
            {
                $i = number_format($i, 2);
                $hourIExplode = explode(".", $i);
                $hourI = $hourIExplode[0] . "." . ((int)$hourIExplode[1]>0 ? (int)$hourIExplode[1]*60/100 : "00");
                $start = number_format($i, 2);
                $end = number_format(str_replace("24", "00", $i+$measure), 2);
                $startExplode = explode(".", $start);
                $start = str_pad($startExplode[0], 2, '0', STR_PAD_LEFT) . "." . ((int)$startExplode[1]>0 ? (int)$startExplode[1]*60/100 : "00");
                $endExplode = explode(".", $end);
                $end = str_pad($endExplode[0], 2, '0', STR_PAD_LEFT) . "." . ((int)$endExplode[1]>0 ? (int)$endExplode[1]*60/100 : "00");
            }
            if($time_format!="H.i")
            {
                $start = date($time_format, strtotime($start));
                $end = date($time_format, strtotime($end));
            }
        
        /*$max_explode = explode(".", $hours->max);
        $max_hour = (int)$hours->max + ((int)$max_explode[1]>0 ? 1 : 0);
        for($i=(int)$hours->min; $i<$max_hour; $i++)
        {
            $start = str_pad($i, 2, '0', STR_PAD_LEFT) . '.00';
            $end = str_replace("24", "00", str_pad($i+1, 2, '0', STR_PAD_LEFT)) . '.00';
            if($time_format!="H.i")
            {
                $start = date($time_format, strtotime($start));
                $end = date($time_format, strtotime($end));
            }*/
            
            $row_empty = true;
            $temp_empty_count = 0;
            $row_content = "";
            for($j=0; $j<count($weekdays); $j++)
            {
                //$weekday_fixed_number = ($weekdays[$j]->menu_order>1 ? $weekdays[$j]->menu_order-1 : 7);
                $weekday_fixed_number = $weekdays[$j]->menu_order;
                if(!in_array($weekday_fixed_number, (array)(isset($drop_columns[$i]["columns"]) ? $drop_columns[$i]["columns"] : array())))
                {   
                    if($this->tt_lib->tt_hour_in_array($i, (isset($event_hours_tt[$weekday_fixed_number]) ? $event_hours_tt[$weekday_fixed_number] : array()), $measure, $hours_min))
                    {
                        $rowspan = $this->tt_lib->tt_get_rowspan_value($i, $event_hours_tt[$weekday_fixed_number], 1, $measure, $hours_min);
                        if($rowspan>1)
                        {
                            if((int)$measure==1)
                            {
                                for($k=1; $k<$rowspan; $k++)
                                    $drop_columns[$i+$k]["columns"][] = $weekday_fixed_number;  
                            }
                            else
                            {
                                for($k=$measure; $k<$rowspan*$measure; $k=$k+$measure)
                                {
                                    $tmp = number_format($i+$k, 2);
                                    $drop_columns["$tmp"]["columns"][] = $weekday_fixed_number; 
                                }
                            }
                        }
                        $array_count = count($event_hours_tt[$weekday_fixed_number]);
                        $hours = array();
                        if((int)$measure==1)
                        {
                            for($k=(int)$i; $k<(int)$i+$rowspan; $k++)
                                $hours[] = $k;
                        }
                        else
                        {
                            for($k=(double)$i; $k<(double)$i+$rowspan*$measure; $k=$k+$measure)
                                $hours[] = $k;
                        }
                        $events = array();
                        for($k=0; $k<$array_count; $k++)
                        {
                            if(((int)$measure==1 && in_array((int)$event_hours_tt[$weekday_fixed_number][$k]["start"], $hours)) OR ((int)$measure!=1 && in_array($this->tt_lib->to_decimal_time($this->tt_lib->roundMin($event_hours_tt[$weekday_fixed_number][$k]["start"], $measure, $hours_min)), $hours)))
                            {
                                /*$events[$event_hours_tt[$weekday_fixed_number][$k]["name"]]["name"] = $event_hours_tt[$weekday_fixed_number][$k]["name"];
                                $events[$event_hours_tt[$weekday_fixed_number][$k]["name"]]["title"] = $event_hours_tt[$weekday_fixed_number][$k]["title"];
                                $events[$event_hours_tt[$weekday_fixed_number][$k]["name"]]["tooltip"][] = $event_hours_tt[$weekday_fixed_number][$k]["tooltip"];
                                $events[$event_hours_tt[$weekday_fixed_number][$k]["name"]]["before_hour_text"][] = $event_hours_tt[$weekday_fixed_number][$k]["before_hour_text"];
                                $events[$event_hours_tt[$weekday_fixed_number][$k]["name"]]["after_hour_text"][] = $event_hours_tt[$weekday_fixed_number][$k]["after_hour_text"];
                                $events[$event_hours_tt[$weekday_fixed_number][$k]["name"]]["id"] = $event_hours_tt[$weekday_fixed_number][$k]["id"];
                                $events[$event_hours_tt[$weekday_fixed_number][$k]["name"]]["hours"][] = $event_hours_tt[$weekday_fixed_number][$k]["start"] . " - " . $event_hours_tt[$weekday_fixed_number][$k]["end"];*/
                                $events[$k]["name"] = $event_hours_tt[$weekday_fixed_number][$k]["name"];
                                $events[$k]["title"] = $event_hours_tt[$weekday_fixed_number][$k]["title"];
                                $events[$k]["tooltip"][] = $event_hours_tt[$weekday_fixed_number][$k]["tooltip"];
                                $events[$k]["before_hour_text"][] = $event_hours_tt[$weekday_fixed_number][$k]["before_hour_text"];
                                $events[$k]["after_hour_text"][] = $event_hours_tt[$weekday_fixed_number][$k]["after_hour_text"];
                                $events[$k]["available_places"][] = $event_hours_tt[$weekday_fixed_number][$k]["available_places"];
                                $events[$k]["booking_count"][] = $event_hours_tt[$weekday_fixed_number][$k]["booking_count"];
                                $events[$k]["current_user_booking_id"][] = $event_hours_tt[$weekday_fixed_number][$k]["current_user_booking_id"];
                                $events[$k]["event_hours_id"][] = $event_hours_tt[$weekday_fixed_number][$k]["event_hours_id"];
                                $events[$k]["id"] = $event_hours_tt[$weekday_fixed_number][$k]["id"];
                                $events[$k]["hours"][] = $event_hours_tt[$weekday_fixed_number][$k]["start"] . " - " . $event_hours_tt[$weekday_fixed_number][$k]["end"];
                                $events[$k]["color_bg"] = $event_hours_tt[$weekday_fixed_number][$k]["color_bg"];
                                $events[$k]["color_hover"] = $event_hours_tt[$weekday_fixed_number][$k]["color_hover"];
                                $event_hours_tt[$weekday_fixed_number][$k]["displayed"] = true;
                            }
                        }
                        $color = "";
                        $text_color = "";
                        $hover_color = "";
                        $hover_text_color = "";
                        $hours_text_color = "";
                        $hours_hover_text_color = "";
                        if(count($events)==1 && count($events[key($events)]['hours'])==1)
                        {
                            $color = $events[key($events)]["color_bg"];
                            if($color=="" && strtoupper($box_bg_color)!="00A27C") $color = $box_bg_color;

                            $hover_color = $events[key($events)]["color_hover"];
                            if($hover_color=="" && strtoupper($box_hover_bg_color)!="1F736A") $hover_color = $box_hover_bg_color;

                            $text_color = "";
                            if($text_color=="" && strtoupper($box_txt_color)!="FFFFFF") $text_color = $box_txt_color;

                            $hover_text_color = "";
                            if($hover_text_color=="" && strtoupper($box_hover_txt_color)!="FFFFFF")
                            {
                                $hover_text_color = $box_hover_txt_color;
                                if($text_color=="") $text_color = "FFFFFF";
                            }
                            $hours_text_color = "";
                            if($hours_text_color=="" && strtoupper($box_hours_txt_color)!="FFFFFF") $hours_text_color = $box_hours_txt_color;

                            $hours_hover_text_color = "";
                            if($hours_hover_text_color=="" && (strtoupper($box_hours_hover_txt_color)!="FFFFFF" OR $hours_text_color!=""))
                            {
                                $hours_hover_text_color = $box_hours_hover_txt_color;
                                if($hours_text_color=="") $hours_text_color = "FFFFFF";
                            }
                        }
                        
                        $booking_text_color = ($booking_text_color!="" && strtoupper($booking_text_color)!="FFFFFF" ? $booking_text_color : "");
                        $booking_bg_color = ($booking_bg_color!="" &&strtoupper( $booking_bg_color)!="05BB90" ? $booking_bg_color : "");
                        $booking_hover_text_color = ($booking_hover_text_color!="" && strtoupper($booking_hover_text_color)!="FFFFFF" ? $booking_hover_text_color : "");
                        $booking_hover_bg_color = ($booking_hover_bg_color!="" && strtoupper($booking_hover_bg_color)!="FFFFFF" ? $booking_hover_bg_color : "");
                        $unavailable_text_color = ($unavailable_text_color!="" && strtoupper($unavailable_text_color)!="AAAAAA" ? $unavailable_text_color : "");
                        $unavailable_bg_color = ($unavailable_bg_color!="" && strtoupper($unavailable_bg_color)!="EEEEEE" ? $unavailable_bg_color : "");
                        $booked_text_color = ($booked_text_color!="" && strtoupper($booked_text_color)!="AAAAAA" ? $booked_text_color : "");
                        $booked_bg_color = ($booked_bg_color!="" && strtoupper($booked_bg_color)!="EEEEEE" ? $booked_bg_color : "");
                        $available_slots_color = ($available_slots_color!="" && strtoupper($available_slots_color)!="FFD544" ? $available_slots_color : "");

                        $global_colors = array(
                            "box_bg_color" => $box_bg_color,
                            "box_hover_bg_color" => $box_hover_bg_color,
                            "box_txt_color" => $box_txt_color,
                            "box_hover_txt_color" => $box_hover_txt_color,
                            "box_hours_txt_color" => $box_hours_txt_color,
                            "box_hours_hover_txt_color" => $box_hours_hover_txt_color,
                            "booking_text_color" => ($booking_text_color),
                            "booking_bg_color" => ($booking_bg_color),
                            "booking_hover_text_color" => ($booking_hover_text_color),
                            "booking_hover_bg_color" => ($booking_hover_bg_color),
                            "booked_text_color" => ($booked_text_color),
                            "booked_bg_color" => ($booked_bg_color),
                            "unavailable_text_color" => ($unavailable_text_color),
                            "unavailable_bg_color" => ($unavailable_bg_color),
                            "available_slots_color" => ($available_slots_color),
                        );
                        $row_content .= '<td' . ($color!="" OR $text_color!="" OR $text_align!="center" ? ' style="' . ($text_align!="center" ? 'text-align:' . $text_align . ';' : '') . ($color!="" ? 'background: #' . $color . ';' : '') . ($text_color!="" ? 'color: #' . $text_color . ';' : '') . '"': '') . ($hover_color!="" OR $hover_text_color!="" OR $hours_hover_text_color!="" ? ' onMouseOver="' . ($hover_color!="" ? 'this.style.background=\'#'.$hover_color.'\';' : '') . ($hover_text_color!="" ? 'this.style.color=\'#'.$hover_text_color.'\';jQuery(this).find(\'.event_header\').css(\'cssText\', \'color: #'.$hover_text_color.' !important\');' : '') . ($hours_hover_text_color!="" ? 'jQuery(this).find(\'.hours\').css(\'color\',\'#'.$hours_hover_text_color.'\');' : '') . '" onMouseOut="' . ($hover_color!="" ? 'this.style.background=\'#'.$color.'\';' : '') . ($hover_text_color!="" ? 'this.style.color=\'#'.$text_color.'\';jQuery(this).find(\'.event_header\').css(\'cssText\',\'color: #'.$text_color.' !important\');' : '') . ($hours_hover_text_color!="" ? 'jQuery(this).find(\'.hours\').css(\'color\',\'#'.$hours_text_color.'\');' : '') . '"' : '') . ' class="event' . (count(array_filter(array_values($events[key($events)]['tooltip']))) && count($events)==1 && count($events[key($events)]['hours'])==1 ? ' tt_tooltip' : '' ) . (count($events)==1 && count($events[key($events)]['hours'])==1 ? ' tt_single_event' : '') . '"' . ($rowspan>1 ? ' rowspan="' . $rowspan . '"' : '') . '>';
                        $row_content .= $this->tt_get_row_content($events, compact("events_page", "time_format", "event_layout", "global_colors", "disable_event_url", "show_booking_button", "show_available_slots", "booking_label", "booked_label", "unavailable_label"));
                        $row_content .= '</td>';
                        $row_empty = false;
                    }
                    else
                        $row_content .= '<td></td>';
                    $temp_empty_count++;
                }
            }
            if($temp_empty_count!=$j)
                $row_empty = false;
            if(((int)$hide_empty && !$row_empty) OR !(int)$hide_empty)
            {
                $output .= '<tr class="row_' . ($l+1) . ($l%2==1 ? ' row_gray' : '') . '"' . ($l%2==1 && strtoupper($row1_color)!="F0F0F0" ? ' style="background: ' . ($row1_color!="transparent" ? '#' : '') . $row1_color . ' !important;"' : '') . ($l%2==0 && $row2_color!="" ? ' style="background: ' . ($row2_color!="transparent" ? '#' : '') . $row2_color . ' !important;"' : '') . '>';
                if(!(int)$hide_hours_column)
                {
                    $output .= '<td class="tt_hours_column">
                        ' . $start . ((int)$show_end_hour ? ' - ' . $end : '') . '
                    </td>';
                }
                $output .= $row_content;                
                $output .= '</tr>';
                $l++;
            }
        }
        $output .= '</tbody>
                </table>';
        if((int)$responsive)
        {
            $output .= '<div class="tt_timetable small ' . ($colors_responsive_mode ? 'use_colors' : '') . '">';
            $l = 0;
            foreach($weekdays as $weekday)
            {
                //$weekday_fixed_number = ($weekday->menu_order>1 ? $weekday->menu_order-1 : 7);
                $weekday_fixed_number = $weekday->menu_order;
                if(isset($event_hours_tt[$weekday_fixed_number]))
                {
                    $output .= '<h3 class="box_header ' . ($collapse_event_hours_responsive ? 'plus ' : '') . ($l>0 ? ' page_margin_top' : '') . '">
                        ' . $weekday->title . '
                    </h3>
                    <ul class="tt_items_list thin page_margin_top timetable_clearfix' . (isset($mode) && $mode=='12h' ? ' mode12' : '') . '">';
                        $event_hours_count = count($event_hours_tt[$weekday_fixed_number]);
                            
                        for($i=0; $i<$event_hours_count; $i++)
                        {
                            if($time_format!="H.i")
                            {
                                $event_hours_tt[$weekday_fixed_number][$i]["start"] = date($time_format, strtotime($event_hours_tt[$weekday_fixed_number][$i]["start"]));
                                $event_hours_tt[$weekday_fixed_number][$i]["end"] = date($time_format, strtotime($event_hours_tt[$weekday_fixed_number][$i]["end"]));
                            }
                            $classes_url = "";
                            $timetable_custom_url = "";
                            // if(!(int)$this->tt->get_post_meta($event_hours_tt[$weekday_fixed_number][$i]["id"], "timetable_disable_url", true) && !(int)$disable_event_url)
                            //     $classes_url = ($timetable_custom_url!="" ? $timetable_custom_url : '');//get_permalink($event_hours_tt[$weekday_fixed_number][$i]["id"]));
                            
                            $colors_html = '';
                            $list_colors_html = '';
                            $text_colors_html = '';
                            $hours_text_colors_html = '';
                            
                            if($colors_responsive_mode)
                            {
                                $color = $event_hours_tt[$weekday_fixed_number][$i]["color_bg"];
                                if($color=="" && strtoupper($box_bg_color)!="00A27C")
                                    $color = $box_bg_color;
                                $hover_color = $event_hours_tt[$weekday_fixed_number][$i]["color_hover"];
                                if($hover_color=="" && strtoupper($box_hover_bg_color)!="1F736A")
                                    $hover_color = $box_hover_bg_color;
                                $text_color = "";
                                if($text_color=="" && strtoupper($box_txt_color)!="FFFFFF")
                                    $text_color = $box_txt_color;
                                $hover_text_color = "";
                                if($hover_text_color=="" && strtoupper($box_hover_txt_color)!="FFFFFF")
                                {
                                    $hover_text_color = $box_hover_txt_color;
                                    if($text_color=="")
                                        $text_color = "FFFFFF";
                                }
                                $hours_text_color = "";
                                if($hours_text_color=="" && strtoupper($box_hours_txt_color)!="FFFFFF")
                                    $hours_text_color = $box_hours_txt_color;
                                $hours_hover_text_color = "";
                                if($hours_hover_text_color=="" && (strtoupper($box_hours_hover_txt_color)!="FFFFFF" OR $hours_text_color!=""))
                                {
                                    $hours_hover_text_color = $box_hours_hover_txt_color;
                                    if($hours_text_color=="")
                                        $hours_text_color = "FFFFFF";
                                }
                                
                                $colors_html = ($color!="" OR $text_color!="" ? ' style="' . ($color!="" ? 'background: #' . $color . ';' : '') . ($text_color!="" ? 'color: #' . $text_color . ';' : '') . '"': '') . ($hover_color!="" OR $hover_text_color!="" OR $hours_hover_text_color!="" ? ' onMouseOver="' . ($hover_color!="" ? 'this.style.background=\'#'.$hover_color.'\';' : '') . ($hover_text_color!="" ? 'this.style.color=\'#'.$hover_text_color.'\';jQuery(this).find(\'.event_header,.event_description\').css(\'cssText\', \'color: #'.$hover_text_color.' !important\');' : '') . ($hours_hover_text_color!="" ? 'jQuery(this).find(\'.value\').css(\'color\',\'#'.$hours_hover_text_color.'\');' : '') . '" onMouseOut="' . ($hover_color!="" ? 'this.style.background=\'#'.$color.'\';' : '') . ($hover_text_color!="" ? 'this.style.color=\'#'.$text_color.'\';jQuery(this).find(\'.event_header,.event_description\').css(\'cssText\',\'color: #'.$text_color.' !important\');' : '') . ($hours_hover_text_color!="" ? 'jQuery(this).find(\'.value\').css(\'color\',\'#'.$hours_text_color.'\');' : '') . '"' : '');
                                $text_colors_html = ($text_color!="" ? ' style="color: #' . $text_color . ' !important;"' : '');
                                $hours_text_colors_html = ($hours_text_color!="" ? ' style="color:#' . $hours_text_color . ';"' : '');
                            }
                            
                            $output .= '
                                <li ' . $colors_html . ' class="timetable_clearfix">
                                    <div class="event_container">
                                        <' . ($classes_url!="" ? 'a' : 'span') . ($classes_url!="" ? ' href="' . $classes_url . '"' : '') . ' title="' .  $event_hours_tt[$weekday_fixed_number][$i]["title"] . '"' . ' class="event_header" ' . $text_colors_html . '>' . $event_hours_tt[$weekday_fixed_number][$i]["title"] . ' </' . ($classes_url!="" ? 'a' : 'span') . '>';
                            
                            if(in_array($event_description_responsive, array("description-2", "description-1", "description-1-and-description-2")) && ($event_hours_tt[$weekday_fixed_number][$i]["before_hour_text"] OR $event_hours_tt[$weekday_fixed_number][$i]["after_hour_text"]))
                            {
                                $output .= '<span class="event_description" ' . $text_colors_html . '>'.
                                    (in_array($event_description_responsive, array("description-1", "description-1-and-description-2")) ? $event_hours_tt[$weekday_fixed_number][$i]["before_hour_text"] : '') .
                                    (in_array($event_description_responsive, array("description-1-and-description-2")) && $event_hours_tt[$weekday_fixed_number][$i]["before_hour_text"]!="" && $event_hours_tt[$weekday_fixed_number][$i]["after_hour_text"]!="" ? ' &middot; ' : '') . 
                                    (in_array($event_description_responsive, array("description-2", "description-1-and-description-2")) ? $event_hours_tt[$weekday_fixed_number][$i]["after_hour_text"] : '') .
                                '</span>';
                            }

                            $available_slots = $event_hours_tt[$weekday_fixed_number][$i]["available_places"]-$event_hours_tt[$weekday_fixed_number][$i]["booking_count"];
                            if($show_booking_button!="no" && $show_available_slots!="no" && $available_slots)
                            {
                                $output .= '<span class="available_slots id-' . $event_hours_tt[$weekday_fixed_number][$i]["event_hours_id"] . '">'.
                                        '<span class="count">' . $available_slots . '</span> ' . 
                                        ($available_slots==1 ? "slot available" : "slots available")  . 
                                    '</span>';
                            }
                            
                            $output .= '</div>';
                            
    //                      $event_hours_tt[$weekday_fixed_number][$i]["event_hours_id"]
                            
                            $output .= '<div class="value" ' . $hours_text_colors_html . '>
                                        ' . $event_hours_tt[$weekday_fixed_number][$i]["start"] . ' - ' . $event_hours_tt[$weekday_fixed_number][$i]["end"];
                            
                            if($show_booking_button!="no")
                            {
                                if((int)$event_hours_tt[$weekday_fixed_number][$i]["current_user_booking_id"])
                                {
                                    $output .= "<div class='event_hour_booking_wrapper " . $show_booking_button . "'>
                                                    <a href='#' class='event_hour_booking id-" . $event_hours_tt[$weekday_fixed_number][$i]["event_hours_id"] . " booked " . $show_booking_button . "' style='" . (strlen($booked_text_color) ? " color: #" . $booked_text_color . " !important;" : "") . (strlen($booked_bg_color) ? " background-color: #" . $booked_bg_color . " !important;" : "") . "' title='" . $booked_label . "'>" . $booked_label . "</a>
                                                </div>";
                                }
                                elseif(!$available_slots)
                                {
                                    $output .= "<div class='event_hour_booking_wrapper " . $show_booking_button . "'>
                                                    <a href='#' class='event_hour_booking id-" . $event_hours_tt[$weekday_fixed_number][$i]["event_hours_id"] . " unavailable " . $show_booking_button . "' style='" . (strlen($unavailable_text_color) ? " color: #" . $unavailable_text_color . " !important;" : "") . (strlen($unavailable_bg_color) ? " background-color: #" . $unavailable_bg_color . " !important;" : "") . "' title='" . $unavailable_label . "'>" . $unavailable_label . "</a>
                                                </div>";
                                }
                                else
                                {
                                    $output .= "<div class='event_hour_booking_wrapper " . $show_booking_button . "'>
                                                    <a href='#' class='event_hour_booking id-" . $event_hours_tt[$weekday_fixed_number][$i]["event_hours_id"] . " " . $show_booking_button . "' data-event-hour-id='" . $event_hours_tt[$weekday_fixed_number][$i]["event_hours_id"] . "' style='" . (strlen($booking_text_color) ? " color: #" . $booking_text_color . " !important;" : "") . (strlen($booking_bg_color) ? " background-color: #" . $booking_bg_color . ";" : "") . "' onMouseOver='" . (strlen($booking_hover_text_color) ? " this.style.setProperty(\"color\", \"#" . $booking_hover_text_color . "\", \"important\");" : "") . (strlen($booking_hover_bg_color) ? " this.style.setProperty(\"background\", \"#" . $booking_hover_bg_color . "\", \"important\");" : "") . "' onMouseOut='" . (strlen($booking_hover_text_color) ? (strlen($booking_text_color) ? " this.style.setProperty(\"color\", \"#" . $booking_text_color . "\", \"important\");" : " this.style.color=\"\";") : "") . (strlen($booking_hover_bg_color) ? (strlen($booking_bg_color) ? " this.style.setProperty(\"background\", \"#" . $booking_bg_color . "\", \"important\");" : " this.style.background=\"\";") : "") . "' title='" . $booking_label . "'>" . $booking_label . "</a>
                                                </div>";
                                }
                            }
                            
                            $output .=  '</div>
                                </li>';
                        }
                    $output .= '</ul>';
                    $l++;
                }
            }
            $output .= '</div>';
        }
        return $output;
    }

    function tt_get_row_content($events, $args)
    {
        extract($args);
        $content = "";
        foreach($events as $key=>$details)
        {
            $color = "";
            $hover_color = "";
            $textcolor = "";
            $hover_text_color = "";
            $hours_text_color = "";
            $hours_count = count($details["hours"]);
            if(count($events)>1 OR (count($events)==1 && $hours_count>1))
            {
                $color = $details["color_bg"];
                if($color=="" && strtoupper($global_colors["box_bg_color"])!="00A27C")
                    $color = $global_colors["box_bg_color"];
                $hover_color = $details["color_hover"];
                if($hover_color=="" && strtoupper($global_colors["box_hover_bg_color"])!="1F736A")
                    $hover_color = $global_colors["box_hover_bg_color"];
            }
            $text_color = "";
            if($text_color=="" && strtoupper($global_colors["box_txt_color"])!="FFFFFF")
                $text_color = $global_colors["box_txt_color"];
            $hover_text_color = "";
            if($hover_text_color=="" && strtoupper($global_colors["box_hover_txt_color"])!="FFFFFF")
            {
                $hover_text_color = $global_colors["box_hover_txt_color"];
                if($text_color=="")
                    $text_color = "FFFFFF";
            }
            $hours_text_color = "";
            if($hours_text_color=="" && strtoupper($global_colors["box_hours_txt_color"])!="FFFFFF")
                $hours_text_color = $global_colors["box_hours_txt_color"];
            $hours_hover_text_color = "";
            if($hours_hover_text_color=="" && (strtoupper($global_colors["box_hours_hover_txt_color"])!="FFFFFF" OR $hours_text_color!=""))
            {
                $hours_hover_text_color = $global_colors["box_hours_hover_txt_color"];
                if($hours_text_color=="")
                    $hours_text_color = "FFFFFF";
            }
            
            extract($global_colors);
    //      $booking_text_color = ($global_colors['booking_text_color']!="" && $global_colors['booking_text_color']!="FFFFFF" ? $global_colors['booking_text_color'] : "");
    //      $booking_bg_color = ($global_colors['booking_bg_color']!="" && $global_colors['booking_bg_color']!="05BB90" ? $global_colors['booking_bg_color'] : "");
    //      $booking_hover_text_color = ($global_colors['booking_hover_text_color']!="" && $global_colors['booking_hover_text_color']!="FFFFFF" ? $global_colors['booking_hover_text_color'] : "");
    //      $booking_hover_bg_color = ($global_colors['booking_hover_bg_color']!="" && $global_colors['booking_hover_bg_color']!="FFFFFF" ? $global_colors['booking_hover_bg_color'] : "");
    //      $unavailable_text_color = ($global_colors['unavailable_text_color']!="" && $global_colors['unavailable_text_color']!="AAAAAA" ? $global_colors['unavailable_text_color'] : "");
    //      $unavailable_bg_color = ($global_colors['unavailable_bg_color']!="" && $global_colors['unavailable_bg_color']!="EEEEEE" ? $global_colors['unavailable_bg_color'] : "");
    //      $booked_text_color = ($global_colors['booked_text_color']!="" && $global_colors['booked_text_color']!="AAAAAA" ? $global_colors['booked_text_color'] : "");
    //      $booked_bg_color = ($global_colors['booked_bg_color']!="" && $global_colors['booked_bg_color']!="EEEEEE" ? $global_colors['booked_bg_color'] : "");
    //      $available_slots_color = ($global_colors['available_slots_color']!="" && $global_colors['available_slots_color']!="FFD544" ? $global_colors['available_slots_color'] : "");
            
            $timetable_custom_url = "";
            $classes_url = "";
            // if(!(int)$this->tt->get_post_meta($details["id"], "timetable_disable_url", true) && !(int)$disable_event_url)
            //     $classes_url = ($timetable_custom_url!="" ? $timetable_custom_url : '');//get_permalink($details["id"]));
            
            $class_link = '<' . ($classes_url!="" ? 'a' : 'span') . ' class="event_header"' . ($classes_url!="" ? ' href="' . $classes_url /*. '#' . urldecode($details["name"])*/ . '"' : '') . ' title="' .  $details["title"] . '"' . ($text_color!="" ? ' style="color: #' . $text_color . ' !important;"' : '') . '>' . $details["title"] . '</' . ($classes_url!="" ? 'a' : 'span') . '>';
                    
            for($i=0; $i<$hours_count; $i++)
            {
                $tooltip = "";
                $content .= '<div class="event_container id-' . $details["id"] . (count(array_filter(array_values($details['tooltip']))) && (count($events)>1 OR (count($events)==1 && $hours_count>1)) ? ' tt_tooltip' : '' ) . '"' . ($color!="" OR ($text_color!="" && (count($events)>1 OR (count($events)==1 && $hours_count>1))) ? ' style="' . ($color!="" ? 'background-color: ' . $color . ';' : '') . ($text_color!="" && (count($events)>1 OR (count($events)==1 && $hours_count>1)) ? 'color: #' . $text_color . ';' : '') . '"': '') . (($hover_color!="" OR $hover_text_color!="" OR $hours_hover_text_color!="") && (count($events)>1 OR (count($events)==1 && $hours_count>1)) ? ' onMouseOver="' . ($hover_color!="" ? 'this.style.background=\'#'.$hover_color.'\';' : '') . ($hover_text_color!="" ? 'this.style.color=\'#'.$hover_text_color.'\';jQuery(this).find(\'.event_header\').css(\'cssText\', \'color: #'.$hover_text_color.' !important\');' : '') . ($hours_hover_text_color!="" ? 'jQuery(this).find(\'.hours\').css(\'color\',\'#'.$hours_hover_text_color.'\');' : '') . '" onMouseOut="' . ($hover_color!="" ? 'this.style.background=\''.$color.'\';' : '') . ($hover_text_color!="" ? 'this.style.color=\'#'.$text_color.'\';jQuery(this).find(\'.event_header\').css(\'cssText\',\'color: #'.$text_color.' !important\');' : '') . ($hours_hover_text_color!="" ? 'jQuery(this).find(\'.hours\').css(\'color\',\'#'.$hours_text_color.'\');' : '') . '"' : '') . '>';
                $hoursExplode = explode(" - ", $details["hours"][$i]);
                $startHour = date($time_format, strtotime($hoursExplode[0]));
                $endHour = date($time_format, strtotime($hoursExplode[1]));
                
                $description1_content = "";
                if($details["before_hour_text"][$i]!="")
                    $description1_content = "<div class='before_hour_text'>" . $this->tt_lib->do_shortcode($details["before_hour_text"][$i]) . "</div>";
                $description2_content = "";
                if($details["after_hour_text"][$i]!="")
                    $description2_content = "<div class='after_hour_text'>" . $this->tt_lib->do_shortcode($details["after_hour_text"][$i]) . "</div>";         
                
                $top_hour_content = '<div class="top_hour"><span class="hours"' . ($hours_text_color!="" ? ' style="color:#' . $hours_text_color . ';"' : '') . '>' . $startHour . '</span></div>';
                $bottom_hour_content = '<div class="bottom_hour"><span class="hours"' . ($hours_text_color!="" ? ' style="color:#' . $hours_text_color . ';"' : '') . '>' . $endHour . '</span></div>';
                $hours_content = '<div class="hours_container"><span class="hours"' . ($hours_text_color!="" ? ' style="color:#' . $hours_text_color . ';"' : '') . '>' . $startHour . ' - ' . $endHour . '</span></div>';
                $class_link_tooltip = '<a' . ($hover_text_color!="" ? ' style="color: #' . $hover_text_color . ';"': '') . ' href="' . $classes_url /*. '#' . urldecode($details["name"])*/ . '" title="' .  $details["title"] . '">' . $details["title"] . '</a>';
                $tooltip = ($details["tooltip"][$i]!="" ? $class_link_tooltip : '') . $details["tooltip"][$i];
                
                $booking_content = "";
                $available_slots_html = "";
                if($show_booking_button!="no")
                {
                    $available_slots = $details["available_places"][$i]-$details["booking_count"][$i];
                    if((int)$details["current_user_booking_id"][$i])
                    {
                        $booking_content .= "<a href='#' class='event_hour_booking id-" . $details["event_hours_id"][$i] . " booked' style='" . (strlen($booked_text_color) ? " color: #" . $booked_text_color . " !important;" : "") . (strlen($booked_bg_color) ? " background-color: #" . $booked_bg_color . " !important;" : "") . "' title='" . $booked_label . "'>" . $booked_label . "</a>";
                    }
                    elseif(!$available_slots)
                    {
                        $booking_content .= "<a href='#' class='event_hour_booking id-" . $details["event_hours_id"][$i] . " unavailable' style='" . (strlen($unavailable_text_color) ? " color: #" . $unavailable_text_color . " !important;" : "") . (strlen($unavailable_bg_color) ? " background-color: #" . $unavailable_bg_color . " !important;" : "") . "' title='" . $unavailable_label . "'>" . $unavailable_label . "</a>";
                    }
                    else
                    {
                        $booking_content .= "<a href='#' class='event_hour_booking id-" . $details["event_hours_id"][$i] . "' data-event-hour-id='" . $details["event_hours_id"][$i] . "' style='" . (strlen($booking_text_color) ? " color: #" . $booking_text_color . " !important;" : "") . (strlen($booking_bg_color) ? " background-color: #" . $booking_bg_color . ";" : "") . "' onMouseOver='" . (strlen($booking_hover_text_color) ? " this.style.setProperty(\"color\", \"#" . $booking_hover_text_color . "\", \"important\");" : "") . (strlen($booking_hover_bg_color) ? " this.style.setProperty(\"background\", \"#" . $booking_hover_bg_color . "\", \"important\");" : "") . "' onMouseOut='" . (strlen($booking_hover_text_color) ? (strlen($booking_text_color) ? " this.style.setProperty(\"color\", \"#" . $booking_text_color . "\", \"important\");" : " this.style.color=\"\";") : "") . (strlen($booking_hover_bg_color) ? (strlen($booking_bg_color) ? " this.style.setProperty(\"background\", \"#" . $booking_bg_color . "\", \"important\");" : " this.style.background=\"\";") : "") . "' title='" . $booking_label . "'>" . $booking_label . "</a>";
                    }
                    
                    $booking_content = "<div class='event_hour_booking_wrapper " . $show_booking_button . "'>" . $booking_content . "</div>";
                    
                    if($show_available_slots=="always" && $available_slots)
                    {
                        $available_slots_html = "<span style='" . (strlen($available_slots_color) ? "color: #" . $available_slots_color : "") . "' class='available_slots id-" . $details["event_hours_id"][$i] . "'>
                            <span class='count'>" . $available_slots . "</span> " . 
                            ($available_slots==1 ? "slot available" : "slots available")  . 
                        "</span>";
                    }
                }
                
                if((int)$event_layout==1)
                {
                    $content .= $class_link;
                    $content .= $description1_content;
                    $content .= $top_hour_content;
                    $content .= $bottom_hour_content;
                    $content .= $description2_content;
                }
                else if((int)$event_layout==2)
                {
                    $content .= $top_hour_content;
                    $content .= $bottom_hour_content;
                    $content .= $description1_content;
                    $content .= $class_link;
                    $content .= $description2_content;
                }
                else if((int)$event_layout==3)
                {
                    $content .= $class_link;
                    $content .= $description1_content;
                    $content .= $hours_content;
                    $content .= $description2_content;
                }
                else if((int)$event_layout==4)
                {
                    $content .= $class_link;
                    $content .= $description1_content;
                    $content .= $top_hour_content;
                    $content .= $description2_content;
                }
                else if((int)$event_layout==5)
                {
                    $content .= $class_link;
                    $content .= $description1_content;
                    $content .= $description2_content;
                }
                else if((int)$event_layout==6)
                {
                    $content .= $class_link;
                    $content .= $description1_content;
                    $content .= $hours_content;
                    $content .= $description2_content;
                }
                
                $content .= $available_slots_html;
                
                if(count($events)==1 && $hours_count==1)
                    $content .= '</div>';
                
                if($show_booking_button!="no")
                    $content .= $booking_content;
                
                if($tooltip!="")
                {
                    $hover_color = $details["color_hover"];
                    if($hover_color=="" && strtoupper($global_colors["box_hover_bg_color"])!="1F736A")
                        $hover_color = $global_colors["box_hover_bg_color"];
                    $content .= '<div class="tt_tooltip_text"><div class="tt_tooltip_content"' . ($hover_color!="" OR $hover_text_color!="" ? ' style="' . ($hover_color!="" ? 'background-color: #' . $hover_color . ';' : '') . ($hover_text_color!="" ? 'color: #' . $hover_text_color . ';' : '') . '"': '') . '>' . $tooltip . '</div><div class="tt_tooltip_arrow"' . ($hover_color!="" ? ' style="border-color: #' . $hover_color . ' transparent;"' : '') . '></div></div>';    
                }
                if(count($events)>1 OR (count($events)==1 && $hours_count>1))
                    $content .= '</div>' . (end($events)!=$details OR (end($events)==$details && $i+1<$hours_count) ? '<hr>' : '');
            }
            
            
            /*$content .= $class_link;
            $hours_count = count($details["hours"]);
            for($i=0; $i<$hours_count; $i++)
            {
                if($time_format!="H.i")
                {
                    $hoursExplode = explode(" - ", $details["hours"][$i]);
                    $details["hours"][$i] = date($time_format, strtotime($hoursExplode[0])) . " - " . date($time_format, strtotime($hoursExplode[1]));
                }
                $content .= ($i!=0 ? '<br />' : '');
                if($details["before_hour_text"][$i]!="")
                    $content .= "<div class='before_hour_text'>" . $details["before_hour_text"][$i] . "</div>";
                $content .= '<span class="hours"' . ($hours_text_color!="" ? ' style="color:#' . $hours_text_color . ';"' : '') . '>' . $details["hours"][$i] . '</span>';
                if($details["after_hour_text"][$i]!="")
                    $content .= "<div class='after_hour_text'>" . $details["after_hour_text"][$i] . "</div>";
                $class_link_tooltip = '<a' . ($hover_text_color!="" ? ' style="color: #' . $hover_text_color . ';"': '') . ' href="' . $classes_url . '#' . urldecode($details["name"]) . '" title="' .  esc_attr($key) . '">' . $key . '</a>';
                $tooltip .= ($tooltip!="" && $details["tooltip"][$i]!="" ? '<br /><br />' : '' ) . ($details["tooltip"][$i]!="" ? $class_link_tooltip : '') . $details["tooltip"][$i];
            }*/
            /*if(count($events)==1)
                $content .= '</div>';
            if($tooltip!="")
            {
                $hover_color = $this->tt->get_post_meta($details["id"], "timetable_hover_color", true);
                $content .= '<div class="tooltip_text"><div class="tooltip_content"' . ($hover_color!="" OR $hover_text_color!="" ? ' style="' . ($hover_color!="" ? 'background-color: #' . $hover_color . ';' : '') . ($hover_text_color!="" ? 'color: #' . $hover_text_color . ';' : '') . '"': '') . '>' . $tooltip . '</div><span class="tooltip_arrow"' . ($hover_color!="" ? ' style="border-color: #' . $hover_color . ' transparent;"' : '') . '></span></div>';   
            }
            
            if(count($events)>1)
                $content .= '</div>' . (end($events)!=$details ? '<hr>' : '');*/
        }
        return $content;
    }

}


?>