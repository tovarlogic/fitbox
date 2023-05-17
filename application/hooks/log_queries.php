<?php

    class Db_log {
       function logQueries() {
            $CI = & get_instance();
            $filepath = APPPATH . 'logs/Query-log-' . date('Y-m-d') . '.php'; 
            $handle = fopen($filepath, "a+");                        

            $times = $CI->db->query_times;
            foreach ($CI->db->queries as $key => $query){ 
                $sql = $query . " \n Execution Time:" . $times[$key]; 

                fwrite($handle, $sql . "\n\n");    
            }
             fclose($handle);  

            //  $handle = fopen($filepath, "a+"); 
            // $times2 = $CI->db2->query_times;
            // foreach ($CI->db2->queries as $key2 => $query2){ 
            //     $sql2 = $query2 . " \n Execution Time:" . $times2[$key2]; 

            //     fwrite($handle, $sql2 . "\n\n");    
            // }
            // fclose($handle);  

            // $handle = fopen($filepath, "a+"); 
            // $times3 = $CI->db3->query_times;
            // foreach ($CI->db3->queries as $key3 => $query3){ 
            //     $sql3 = $query3 . " \n Execution Time:" . $times3[$key3]; 

            //     fwrite($handle, $sql3 . "\n\n");    
            // }

            // fclose($handle);  


        }

    }
?>