<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Front
{

    /**
     * Front main default construct for do basic things
     * @author 23 Digital
     */
    public static $per_page = 10;

    public function __construct()
    {
        parent::__construct();


        if(!$this->input->is_ajax_request())
        {
            if(isset($_SERVER['HTTP_REFERER']))
            {
                $this->session->set_userdata('HTTP_REFERER', $_SERVER['HTTP_REFERER']);
            }
            if(isset($_SERVER['REDIRECT_SCRIPT_URI']))
            {
                $this->session->set_userdata('REDIRECT_URL', $_SERVER['REDIRECT_SCRIPT_URI']);
            }
            else if(isset($_SERVER['REDIRECT_URL']))
            {
                if(liveMode==0)
                    $this->session->set_userdata('REDIRECT_URL', $_SERVER['REDIRECT_URL']);
                else
                    $this->session->set_userdata('REDIRECT_URL', $_SERVER['REDIRECT_QUERY_STRING']);
            }

            //pr($_SERVER) ;
        }

        //echo  $this->router->fetch_class();
    }

    /**
     * Index Page for this controller.
     * @author 23 Digital
     */
    public function index($data = "")
    {
        if(isset($data) && !empty($data))
        {
            $this->data['set_pass'] = base64_decode($data);
        }

        if($this->session->has_userdata('room_filter'))
        {
            $room_filter = $this->session->userdata('room_filter');
            $this->data['room_filter'] = $room_filter;
        }

        $this->db->order_by("b.sort = 0", 'ASC', false);
        $this->db->order_by("b.sort", 'ASC', false);
        $this->data['banners'] = $this->app_model->find('banners b', 'all', '', array('b.is_active' => 1));

        $this->db->order_by("pb.sort = 0", 'ASC', false);
        $this->db->order_by("pb.sort", 'ASC', false);
        $this->data['promotional_banners'] = $this->app_model->find('promotional_banners pb', 'all', '', array('pb.status' => 1));

        $this->data['aboutUs'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'about-us', 'p.status' => 1));
        $this->db->order_by("fp.sort", 'ASC', false);
        $this->data['business_partner'] = $this->app_model->find('featuredpartners fp', 'all', array('fp.id', 'fp.partner_image', 'fp.name', 'fp.link'), array('fp.is_active' => 1));

        $this->db->order_by('activities_name', 'ASC');
        $wellnessactivities_array = $this->app_model->find('wellness_treatment_activities wta', 'all', array('wta.*', 'wta.id as activities_id', 'wta.name as activities_name'), array('wta.is_active' => 1, 'wta.is_home' => 1));

        $this->data['wellnessactivities_array'] = $wellnessactivities_array;

        $this->db->order_by('ws.sort', 'ASC');
        $wellness_array = $this->app_model->find('wellness_styles ws', 'all', array('ws.*', 'ws.id as wellness_id', 'ws.name as wellness_name'), array('ws.is_active' => 1, 'ws.is_home' => 1));
        $this->data['wellness_array'] = $wellness_array;

        $this->db->order_by('td.sort', 'ASC');
        $topdastination_array = $this->app_model->find('top_destination td', 'all', array('td.*', 'td.id as top_destination_id', 'c.country_name as country_name', 'city.city_name as city_name'), array('td.is_active' => 1, 'td.is_home' => 1), array(array('countries c', 'c.country_id=td.country_id AND c.is_active=1 AND c.is_deleted=0',
            'inner'),
            array('cities city', 'city.city_id=td.city_id AND city.is_active=1 AND city.is_deleted=0',
                'left')
        ));

        $this->data['topdastination_array'] = $topdastination_array;
        $date = new DateTime("now");
        $curr_date = $date->format('Y-m-d ');
        $this->db->group_by('hsd.promoId');
        $this->db->where('hsd.effectiveEndDate >=', $curr_date);
        $this->db->where('hsd.effectiveStartDate <=', $curr_date);
        $this->db->order_by('hi.is_main', 'DESC');
        $deal_arr = $this->app_model->find('hotel_select_deals hsd', 'all',
            array('hsd.*', 'hi.*', 'hsd.promoId as deal_id', 'hsd.promoDescription as deals_name'),
            array('hsd.is_active' => 1, 'hsd.is_home' => 1), array(
                array('hotel_images hi', 'hsd.hotelId=hi.EANHotelID', 'left')
            ));


        $deals_array = array();
        if(isset($deal_arr) && !empty($deal_arr))
        {
            $i = 0 ;
            foreach($deal_arr as $deal)
            {
                $deals_array[$i] = $deal ;
                $this->db->order_by('i.is_main','DESC');
                $hotelImages =  $this->app_model->find('hotel_images i','first','*',array('i.EANHotelID'=>$deal['hotelId'])) ;
                $deals_array[$i]['URL'] = isset($hotelImages['URL'])?$hotelImages['URL']:'' ;
                $i++ ;
            }
        }


//        pr($deal_arr);



        //  pr($deals_array);
        $hotels_ids = $this->get_all_hotel_ids_from_deals($deals_array);
        $hotel_live_data = $this->get_live_data_from_hotel_ids($hotels_ids);
        //pr($hotel_live_data);
        $makefinaldeals = $this->create_final_deals_array($deals_array, $hotel_live_data);

        $this->data['deals_array'] = $makefinaldeals;

        // $this->data['pre_phone_list'] = $this->app_model->find('countries c', 'list', array('c.phonecode as phonecode', 'c.iso3 as codename'), array('c.is_active' => 1));
        $this->template->write('title', 'Book Online Wellness Hotels, Spas, Resorts & Retreats | In This Life Wellness Travel');
        $this->template->write('meta_description', 'World’s only Wellness Travel Company with live availability. Search, Find and book your Wellness Hotel, Spa, Resort & Retreat, Yoga retreat, Meditation Retreat and more.');
        $this->template->write('meta_keywords', COMPANY_NAME . " - " . COMPANY_TAGLINE);
        $this->template->write_view('content', 'front/home/index', $this->data);
        $this->template->render();
    }

    /**
     * Industry Index Page for this controller.
     * @author Josh Curtis - duplicated from 'index' function
     */
    public function industryindex($data = "")
    {
        if(isset($data) && !empty($data))
        {
            $this->data['set_pass'] = base64_decode($data);
        }

        if($this->session->has_userdata('room_filter'))
        {
            $room_filter = $this->session->userdata('room_filter');
            $this->data['room_filter'] = $room_filter;
        }

        $this->db->order_by("b.sort = 0", 'ASC', false);
        $this->db->order_by("b.sort", 'ASC', false);
        $this->data['banners'] = $this->app_model->find('banners b', 'all', '', array('b.is_active' => 1));

        $this->db->order_by("pb.sort = 0", 'ASC', false);
        $this->db->order_by("pb.sort", 'ASC', false);
        $this->data['promotional_banners'] = $this->app_model->find('promotional_banners pb', 'all', '', array('pb.status' => 1));

        $this->data['aboutUs'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'about-us', 'p.status' => 1));
        $this->db->order_by("fp.sort", 'ASC', false);
        $this->data['business_partner'] = $this->app_model->find('featuredpartners fp', 'all', array('fp.id', 'fp.partner_image', 'fp.name', 'fp.link'), array('fp.is_active' => 1));

        $this->db->order_by('activities_name', 'ASC');
        $wellnessactivities_array = $this->app_model->find('wellness_treatment_activities wta', 'all', array('wta.*', 'wta.id as activities_id', 'wta.name as activities_name'), array('wta.is_active' => 1, 'wta.is_home' => 1));

        $this->data['wellnessactivities_array'] = $wellnessactivities_array;

        $this->db->order_by('ws.sort', 'ASC');
        $wellness_array = $this->app_model->find('wellness_styles ws', 'all', array('ws.*', 'ws.id as wellness_id', 'ws.name as wellness_name'), array('ws.is_active' => 1, 'ws.is_home' => 1));

//        pr($wellness_array);
        $this->data['wellness_array'] = $wellness_array;

        $this->db->order_by('td.sort', 'ASC');
        $topdastination_array = $this->app_model->find('top_destination td', 'all', array('td.*', 'td.id as top_destination_id', 'c.country_name as country_name', 'city.city_name as city_name'), array('td.is_active' => 1, 'td.is_home' => 1), array(array('countries c', 'c.country_id=td.country_id AND c.is_active=1 AND c.is_deleted=0',
            'inner'),
            array('cities city', 'city.city_id=td.city_id AND city.is_active=1 AND city.is_deleted=0',
                'left')
        ));

        $this->data['topdastination_array'] = $topdastination_array;
        $date = new DateTime("now");
        $curr_date = $date->format('Y-m-d ');
        $this->db->group_by('hsd.promoId');
        $this->db->where('hsd.effectiveEndDate >=', $curr_date);
        $this->db->where('hsd.effectiveStartDate <=', $curr_date);
        $this->db->order_by('hi.is_main', 'DESC');
        $deal_arr = $this->app_model->find('hotel_select_deals hsd', 'all',
            array('hsd.*', 'hi.*', 'hsd.promoId as deal_id', 'hsd.promoDescription as deals_name'),
            array('hsd.is_active' => 1, 'hsd.is_home' => 1), array(
                array('hotel_images hi', 'hsd.hotelId=hi.EANHotelID', 'left')
            ));

        $deals_array = array();
        if(isset($deal_arr) && !empty($deal_arr))
        {
            $i = 0 ;
            foreach($deal_arr as $deal)
            {
                $deals_array[$i] = $deal ;
                $this->db->order_by('i.is_main','DESC');
                $hotelImages =  $this->app_model->find('hotel_images i','first','*',array('i.EANHotelID'=>$deal['hotelId'])) ;
                $deals_array[$i]['URL'] = isset($hotelImages['URL'])?$hotelImages['URL']:'' ;
                $i++ ;
            }
        }
        //  pr($deal_arr);
        //  pr($deals_array);
        $hotels_ids = $this->get_all_hotel_ids_from_deals($deals_array);
        $hotel_live_data = $this->get_live_data_from_hotel_ids($hotels_ids);
        //pr($hotel_live_data);
        $makefinaldeals = $this->create_final_deals_array($deals_array, $hotel_live_data);

        $this->data['deals_array'] = $makefinaldeals;

        // $this->data['pre_phone_list'] = $this->app_model->find('countries c', 'list', array('c.phonecode as phonecode', 'c.iso3 as codename'), array('c.is_active' => 1));
        $this->template->write('title', 'Book Online Wellness Hotels, Spas, Resorts & Retreats | In This Life Wellness Travel');
        $this->template->write('meta_description', 'World’s only Wellness Travel Company with live availability. Search, Find and book your Wellness Hotel, Spa, Resort & Retreat, Yoga retreat, Meditation Retreat and more.');
        $this->template->write('meta_keywords', COMPANY_NAME . " - " . COMPANY_TAGLINE);
        $this->template->write_view('content', 'front/home/industryindex', $this->data);
//        pr($this->data);
        $this->template->render();
    }

    public function get_cities_base_on_state_nacl($state_name)
    {
        if($state_name == '193774' || $state_name == '193775')
        {
            $cityData = $this->app_model->find('cities c', 'list', array('c.city_id', 'c.city_name'), array('c.city_id' => $state_name), array(array('states s', 's.state_id=c.state_id', 'LEFT')));
            $state_name = isset($cityData[$state_name]) ? $cityData[$state_name] : 'Bali';
        }
        $cityData = $this->app_model->find('cities c', 'list', array('c.city_id', 'c.city_name'), array('s.state_name' => $state_name), array(array('states s', 's.state_id=c.state_id', 'LEFT')));
        return $cityData;
    }

    public function search($params = '', $params2 = '',$params3='') {
        if (isset($this->session->userdata['customer']) && count($this->session->userdata['customer']) > 0) {
            $temp_data = $params;
            $this->data['footer_active_menu'] = $temp_data;
            $expedia = array();
            $sortByPrice = array();
            $hotelsData = array();
            $this->data['con_country_data'] = array();
            $this->data['categories_activities'] = array();
            $this->data['searchCities'] = $this->get_search_destination_without_country();
            if(isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO']!='search') {
                //echo $_SERVER['PATH_INFO'] ; die ;
                //pr($_SERVER) ;
                $this->data['redirectLink'] = $_SERVER['PATH_INFO'] ;
            }

            if(isset($params) && $params != "") {
                if($params3=='') {
                    $last = $this->uri->total_segments();
                    $params3 = $this->uri->segment($last);
                    $get_data = explode("-", $params3);
                } else {
                    $get_data = explode("-", $params);
                }

                if(isset($get_data) && $get_data[0] == "z") {
                    $cityID = $get_data['1'];
                    $cityName = isset($this->data['searchCities'][$cityID]) ? $this->data['searchCities'][$cityID] : $cityID;
                    $city[] = $cityName;
                }

                if(isset($get_data) && $get_data[0] == "c") {
                    $checkKey = explode(',', $get_data['1']);


                    if(strstr($get_data['1'], '90909') && count($checkKey) == 1) {
                        $get_data[0] = 'z';
                        $city = str_replace('90909', '', $get_data['1']);
                    } else if(strstr($get_data['1'], '90909') && count($checkKey) > 1) {
                        $match = array();
                        foreach($checkKey as $content) {
                            if(substr($content, 0, 5) == '90909') {
                                $matches = substr($content, 5);
                                $city[] = isset($this->data['searchCities'][$matches]) ? $this->data['searchCities'][$matches] : $matches;
                                $get_data['1'] = str_replace('90909' . $matches . ',', '', $get_data['1']);
                                $get_data['1'] = str_replace('90909' . $matches, '', $get_data['1']);
                            }
                        }
                    }
                }
                $wellness_style = "";
                $categories = "";
                if(isset($get_data) && $get_data[0] == "c") {
                    $con_ids = explode(',', $get_data[1]);
                    $this->db->where_in("c.iso2", $con_ids);
                    $country_data1 = $this->app_model->find('countries c', 'all', array('c.country_name'), array('c.is_active' => 1,));

                    if(empty($country_data1)) {
                        $this->db->where_in("c.country_id", $con_ids);
                        $country_data1 = $this->app_model->find('countries c', 'all', array('c.iso2', 'c.country_name'), array('c.is_active' => 1,));
                    }

                    foreach($country_data1 as $ids => $value) {
                        $country_data[] = $value['country_name'];
                    }
                    if(isset($country_data)) {
                        $country_code = $country_data;
                    } else {
                        $country_code = "";
                    }

                }

                if(isset($get_data[0]) && $get_data[0] == "ct") {
                    $continent_id = $get_data[1] ;
                    $list =  get_country_base_on_continent_id($get_data[1]) ;
                    if(isset($list['country'])) {
                        $con_ids = array_values($list['country']) ;
                    }


                    foreach($list['country'] as $name => $id) {
                        $country_data[] = $name ;
                    }
                    if(isset($country_data)) {
                        $country_code = $country_data;
                    } else {
                        $country_code = "";
                    }

                    if(isset($list['cities'])) {

                        $city = array_keys($list['cities']) ;
                    }
                }
                if(isset($get_data) && ($get_data[0] == "s" || (isset($get_data[2]) && $get_data[2] == "s" ))) {
                    if((isset($get_data[2]) && $get_data[2] == "s")) {
                        $wellness_style = $get_data[3];

                        if(isset($get_data[3])) {
                            $wellness_style = $get_data[3];
                        }
                    } else {
                        $wellness_style = $get_data[1];

                        if(isset($get_data[1])) {
                            $wellness_style = $get_data[1];
                        }
                    }
                }
                if(isset($get_data[0]) && $get_data[0] == "a") {
                    //  categories
                    if(isset($get_data[1])) {
                        $categories = $get_data[1];
                    } else {
                        $categories = "";
                    }
                }
                if(isset($get_data[0]) && $get_data[0] == "z") {
                    //  categories
                    if(isset($get_data[1])) {
                        $city = $get_data[1];
                    }
                }

                if(isset($get_data[2]) && $get_data[2] == "a") {
                    //  categories
                    if(isset($get_data[3])) {
                        $categories = $get_data[3];
                    } else {
                        $categories = "";
                    }
                }

                //logic992
                if(isset($get_data[4]) && $get_data[4] == "a") {
                    //  categories
                    if(isset($get_data[5])) {
                        $categories = $get_data[5];
                    } else {
                        $categories = "";
                    }
                }
                if(isset($get_data[2]) && $get_data[2] == "at") {
                    //  categories


                    if(isset($get_data[3])) {
                        //$post['parent_cat'] = $get_data[3] ;
                        $categories = $this->getCategoryTreatmentStyleId($get_data[3]);

                    } else {
                        $categories = "";
                    }
                }

                if(isset($get_data[0]) && $get_data[0] == "at") {
                    //  categories


                    if(isset($get_data[1])) {
                        //$post['parent_cat'] = $get_data[3] ;
                        $categories = $this->getCategoryTreatmentStyleId($get_data[1]);
                    } else {
                        $categories = "";
                    }
                }
                if(isset($get_data[4]) && $get_data[4] == "at") {
                    //  categories


                    if(isset($get_data[5])) {
                        //$post['parent_cat'] = $get_data[3] ;
                        $categories = $this->getCategoryTreatmentStyleId($get_data[5]);
                    } else {
                        $categories = "";
                    }
                }

            }
            $flag = 0;
            if($this->input->is_ajax_request()) {
                $flag = 1;
            }

            $post = $this->input->post();

            if(isset($city) && !empty($city)) {
                $post['city'] = $city ;
            }

            // Seems like this won't be used under params being blank
            $split_for_date = explode('-d', $params2);
            if(isset($split_for_date[1]) && !empty($split_for_date[1])) {
                $post['datefilter'] = str_replace(':', ' - ', $split_for_date[1]);
                $datefilter = str_replace(':', ' - ', $split_for_date[1]);
            }

            // Seems like search_data isn't populated
            if(isset($post['search_data']) && isset($post['datefilterpop'])) {
                $get_data = explode("-", $params2);

                if(isset($get_data) && $get_data[0] == "c") {
                    $con_ids = explode(',', $get_data[1]);
                    $this->db->where_in("c.country_id", $con_ids);
                    $country_data1 = $this->app_model->find('countries c', 'all', array('c.iso2'), array('c.is_active' => 1));
                    foreach($country_data1 as $ids => $value) {
                        $country_data[] = $value['iso2'];
                    }
                    if(isset($country_data)) {
                        $country_code = $country_data;
                    } else {
                        $country_code = "";
                    }
                }
                if(isset($get_data) && $get_data[0] == "s") {
                    // wellness_style =  $get_data[1];
                    if(isset($get_data[1])) {
                        $wellness_style = explode(',', $get_data[1]);
                    } else {
                        $wellness_style = "";
                    }

                }
                if(isset($get_data) && $get_data[0] == "a") {
                    //  categories
                    if(isset($get_data[1])) {
                        $categories = $get_data[1];
                    } else {
                        $categories = "";
                    }
                }

                if(isset($post['datefilterpop'])) {
                    $datefilter = $post['datefilterpop'];
                }
            }

            // Continent IDs ARE set
            if(isset($continent_id) && !empty($continent_id)) {
                $post['continent_id'][] = $continent_id ;
            }

            // There IS a datefilter involved
            if(isset($post['datefilter']) && !empty($post['datefilter'])) {
                $datefilter = $post['datefilter'] ;
            }

            // Country code IS set when country is selected without continent
            // pr($country_code);
            if(isset($country_code)) {
                $post['country_id'] = $country_code;
                $post['datefilter'] = $datefilter;
            }

            if(isset($wellness_style) && !empty($wellness_style)) {
                if(!is_array($wellness_style)) {
                    $wellness_style = explode(',', $wellness_style);
                }
                $post['wellness_style'] = $wellness_style;
            }
            if(isset($categories) && !empty($categories)) {
                if(is_array($categories)) {
                    $post['categories'] = $categories;
                } else {
                    $post['categories'] = explode(',', $categories);
                }
            }
            /* if(empty($post)) {
              $expedia = $this->session->userdata('expediaData');
              $sortByPrice = $this->session->userdata('sortByPrice');
              }
             */

            $this->data['search_destination'] = $this->get_search_destination_without_country();
            $this->data['search_destination']+= $this->get_search_destination_without_continent();
            //pr($this->data['search_destination']) ;


            if($this->session->has_userdata('searchData')) {
                $searchData = $this->session->userdata('searchData');
//            pr($searchData);
            }

            if((!isset($post) || empty($post)) && (isset($searchData) && !empty($searchData)) && $flag == 0) {
                //need to check for low to high & hight to low issue
                $post = $this->session->userdata('searchData');
                if($this->session->has_userdata('expediaData') && !empty($post)) {
                    $expedia = $this->session->userdata('expediaData');
                    $sortByPrice = $this->session->userdata('sortByPrice');
                }
            }
            /*
            if(!isset($post['datefilter'])) {
                $post['datefilter'] = get_date_range() ;
                $datefilter = get_date_range() ;
            }
            */
            //pr($post) ;

            if(isset($post) && !empty($post)) {
                if(isset($post['currency_selector']) && !empty($post['currency_selector'])) {
                    if($this->session->has_userdata('searchData')) {
                        $post = $this->session->userdata('searchData');
                    }

                }

                $postCount = count($post);

                // Replacing post data with session data
                if($this->session->has_userdata('searchData') && $flag == 1) {
                    $ajaxData = $this->input->post();
                    $post = $this->session->userdata('searchData');
                    if(isset($ajaxData['continent_id']))
                        $post['continent_id'] = $ajaxData['continent_id'];
                    else
                        unset($post['continent_id']);
                    if(isset($ajaxData['country_id']))
                        $post['country_id'] = $ajaxData['country_id'];
                    else
                        unset($post['country_id']);
                    if(isset($ajaxData['city']) && !empty($ajaxData['city'])) {
                        $post['city'] = $ajaxData['city'];
                    } else {
                        unset($post['city']);
                    }
                    if(isset($ajaxData['wellness_style']))
                        $post['wellness_style'] = $ajaxData['wellness_style'];
                    else
                        unset($post['wellness_style']);

                    if(isset($ajaxData['parent_cat']))
                        $post['parent_cat'] = $ajaxData['parent_cat'];
                    else
                        unset($post['parent_cat']);

                    if(isset($ajaxData['categories']))
                        $post['categories'] = $ajaxData['categories'];
                    else
                        unset($post['categories']);

                    if(isset($ajaxData['rating']))
                        $post['rating'] = $ajaxData['rating'];
                    else
                        unset($post['rating']);

                    if(isset($ajaxData['price_range'][0]))
                        $post['price_range'] = $ajaxData['price_range'][0];
                    else
                        unset($post['price_range']);

                    if($post['price_range'] == '0,0' || $post['price_range'] == '0,1') {
                        unset($ajaxData['price_range'][0]);
                        unset($post['price_range']);
                    }

                    //added by Rakesh to correct price filter
                    if(isset($ajaxData['price_filter_change']))
                        $post['price_filter_change'] = $ajaxData['price_filter_change'];
                    else
                        unset($post['price_filter_change']);

                    if(isset($ajaxData['min_range']))
                        $post['min_range'] = $ajaxData['min_range'];
                    else
                        unset($post['min_range']);

                    if(isset($ajaxData['max_range']))
                        $post['max_range'] = $ajaxData['max_range'];
                    else
                        unset($post['max_range']);

                    $post['search_text'] = '';
                } else {
                    if(isset($post['rating']))
                        unset($post['rating']);
                    if(isset($post['price_range']))
                        unset($post['price_range']);
                }

                $post['date1'] = '';
                $post['date2'] = '';
                if(!isset($post['datefilter'])) {
                    $post['datefilter'] = get_date_range();
                }
                if(isset($post['datefilter']) && !empty($post['datefilter'])) {
                    $date = explode(' - ', $post['datefilter']);

                    $post['date1'] = format_date($date['0']);
                    $post['date2'] = format_date($date['1']);
                }

                if($this->session->has_userdata('expediaData')) {
                    $this->session->unset_userdata('expediaData');
                }

                if(isset($city) && !empty($city)) {
                    if(isset($city[0])) {
                        $post['city'] = $city;
                    } else {
                        $post['city'][] = $city;
                    }
                }
                $cityList = array();
                $post['cityList'] = '';

                if(isset($post['city']) && (is_array($post['city']) && in_array('Bali', $post['city'])
                        || (isset($city) && $city == '193774')
                        || (is_array($post['city']) && in_array('193774', $post['city'])) )) {
                    $param = 'Bali';
                    $cityList+= $this->get_cities_base_on_state_nacl($param);
                    $post['cityList'] = $cityList;
                    if(isset($city) && !empty($city) && count($city) == 1 && $city == '193774') {
                        unset($post['city']);
                        $post['city'][0] = 'Bali';
                    }
                }
                if(isset($post['city']) && (
                        is_array($post['city'])  && in_array('Hawaii', $post['city']) || (isset($city) && $city == '193775') || (in_array('193775', $post['city'])))) {
                    $param = 'Hawaii';
                    $cityList+= $this->get_cities_base_on_state_nacl($param);
                    $post['cityList'] = $cityList;
                    if(isset($city) && !empty($city) && count($city) == 1 && $city == '193775') {
                        unset($post['city']);
                        $post['city'][0] = "Hawaii";
                    } else if(isset($city) && !empty($city) && count($post['city']) > 1) {
                        $post['city'][2] = "Hawaii";
                    }
                }

                if(isset($post['country_id']) && !empty($post['country_id'])) {
                    if(isset($post['country_id']) && in_array('C1', $post['country_id'])) {
                        $post['country_id']['991'] = 'AG';
                        $post['country_id']['992'] = 'GD';
                        $post['country_id']['993'] = 'DO';
                        $post['country_id']['994'] = 'LC';
                        $post['country_id']['995'] = 'TC';
                        $post['country_id']['996'] = 'DM';
                    }
                }

                if(isset($get_data[0]) && $get_data[0]=='z' && count($get_data)==2) {
                    $flag = 2 ;
                }

                if(isset($post['sort_by']) && !empty($post['sort_by']) && $flag == 0 && (count($post) == 1 || $postCount == 1)) {
                    $sort_by = $post['sort_by'];
                    if($this->session->has_userdata('searchData')) {
                        $post = $this->session->userdata('searchData');
                    }
                    $post['sort_by'] = $sort_by;
                }
                if($this->session->has_userdata('searchData') && count($post) > 1 && $flag == 0) {
                    $this->session->unset_userdata('searchData');
                }

                if(isset($post['search_type']) && !empty($post['search_type']) && $post['search_type'] == 'country') {
                    $post['country_id'][] = $post['search_index'];
                }

                if(isset($post['categories'])) {
                    $this->db->where_in('c.id', $post['categories']);
                    $categories_activities = $this->app_model->find('wellness_treatment_activities c', 'list', array('c.id', 'c.wellness_treatment_activities_categories_id'), array('c.is_active' => 1));

                    if(isset($categories_activities) && !empty($categories_activities))
                        $this->data['categories_activities'] = $categories_activities;
                    else
                        $this->data['categories_activities'] = array();
                }
                $city_where = '';
                $this->data['con_country_data'] = array();

                $flag = 0 ; // dont remove
                if(isset($post['country_id']) && !empty($post['country_id'])) {
                    $this->db->where_in('c.country_name', $post['country_id']);
                    $country_data = $this->app_model->find('manage_search_destinations d', 'list', array('cc.name', 'cc.name'), array('d.is_active' => 1), array(
                        array('manage_continents cc', 'cc.id=d.continent_id AND cc.is_active=1 AND cc.is_deleted=0', 'left'),
                        array('countries c', 'c.iso2=d.country_id AND c.is_active=1 AND c.is_deleted=0', 'left')
                    ));

                    if (isset($country_data) && !empty($country_data)) {
                        $this->data['con_country_data'] = array_keys($country_data);
                    } else {
                        $this->data['con_country_data'] = array();
                    }

                    $city_list = array();

                    //pr($post) ;
                    foreach($post['country_id'] as $country_id) {
                        if($city_where == '') {
                            //$get_city = get_cities_list_base_on_country_id($country_id);
                            if(isset($get_city) && !empty($get_city)) {
                                $city_list = $get_city;
                            }
                            $city_where.=" hs.search_destination = '{$country_id}' ";
                        } else {
                            //$get_city = get_cities_list_base_on_country_id($country_id);
                            if(isset($get_city) && !empty($get_city)) {
                                $city_list+= $get_city;
                            }
                            $city_where.="  OR hs.search_destination = '{$country_id}' ";
                        }
                    }
                }

                if(!isset($post['city']) && !empty($city_list)) {
                    $post['city'] = array();
                    $post['city']+=$city_list;
                }

                if((isset($post['city']) && !empty($post['city']))) {
                    //$post['city']+=$city_list ;

                    $this->db->where_in('city.city_id', $post['city']);
                    $this->db->or_where_in('city.city_name', $post['city']);
                    if(isset($post['cityList']) && !empty($post['cityList'])) {
                        $this->db->or_where_in('city.city_name', $post['cityList']);
                    }
                    $country_data = $this->app_model->find('manage_search_destinations d', 'list', array('cc.name', 'd.country_id'), array('d.is_active' => 1), array(
                        array('manage_continents cc', 'cc.id=d.continent_id AND cc.is_active=1 AND cc.is_deleted=0', 'left'),
                        array('cities city', 'd.city_id=city.city_id AND city.is_active=1 AND city.is_deleted=0', 'left')
                    ));

                    if(isset($country_data) && !empty($country_data)) {
                        $con_country_data = array_keys($country_data);
                        if(isset($con_country_data) && !empty($con_country_data)) {
                            $start = 999;
                            foreach($con_country_data as $contentCountry) {
                                $this->data['con_country_data'][$start] = $contentCountry;
                                $start++;
                            }
                        }

                        $this->data['city_country_data'] = array_values($country_data);
                    } else {
                        //$this->data['con_country_data'] = array() ;
                    }
                }

                $post['con_country_data'] = $this->data['con_country_data'];

                if(isset($post['city'][0]) && !empty($post['city'])) {

                    //$this->db->or_where_in('hs.City', $post['city']);
                    foreach($post['city'] as $city_id) {
                        if($city_where == '') {
                            $city_where.=" hs.search_destination = '{$city_id}' ";
                        } else {
                            $city_where.=" OR hs.search_destination = '{$city_id}' ";
                        }
                    }
                    /* if(isset($post['cityList']) && !empty($post['cityList'])) {
                      $city_where_new = '';
                      foreach($post['cityList'] as $city_id) {
                      if($city_where=='') {
                      $city_where.=" hs.City = '{$city_id}' " ;

                      } else {
                      $city_where.=" OR hs.City = '{$city_id}' " ;
                      }
                      }
                      }
                     */
                    //echo $city_where ; die('123') ;
                }
                if($city_where != '') {
                    $city_where_condition = '(' . $city_where . ' )';
                    //echo $city_where_condition ; die ;
                    $this->db->where($city_where_condition);
                }
                $style_where = '';
                if(isset($post['wellness_style']) && !empty($post['wellness_style'])) {
                    foreach($post['wellness_style'] as $style) {
                        if($style_where == '')
                            $style_where = "FIND_IN_SET({$style},shs.styles_ids) !=0";
                        else
                            $style_where.= " OR  FIND_IN_SET({$style},shs.styles_ids) !=0";
                    }
                }
                if(isset($post['categories']) && !empty($post['categories'])) {
                    //$style_where = '';
                    foreach($post['categories'] as $style) {
                        if($style_where == '') {
                            $style_where = "FIND_IN_SET({$style},shs.activities_ids) !=0";
                        } else {
                            $style_where.= " OR  FIND_IN_SET({$style},shs.activities_ids) !=0";
                        }
                    }
                    //$style_where = '(' . $style_where . ')';
                    //$this->db->where($style_where);
                }

                if(isset($style_where) && !empty($style_where)) {
                    //echo $style_where ; die ;
                    $style_where = '(' . $style_where . ')';
                    $this->db->where($style_where);
                }
                if(isset($post['search_text']) && !empty($post['search_text'])) {
                    $post['search_text'] = trim($post['search_text']) ;
                }
                if((isset($post['search_text']) && !empty($post['search_text'])) && (isset($post['search_index']) && empty($post['search_index'])) && $flag == 0) {
                    $search_text = addslashes(addslashes($post['search_text']));
                    $or_where = '';
                    //$or_where.="(hs.Name LIKE '%{$search_text}%' OR hs.Name LIKE '%{$search_text}'  OR  c.country_name LIKE '%{$search_text}%' OR c.country_name LIKE '%{$search_text}' OR c.continent LIKE '%{$search_text}%'  OR c.continent LIKE '%{$search_text}') OR city.city_name LIKE '%{$search_text}') OR city.city_name LIKE '%{$search_text}%')";
                    $or_where.="(hs.Name LIKE '%{$search_text}%' OR hs.Name LIKE '%{$search_text}'  OR  hs.search_destination LIKE '%{$search_text}%' OR hs.search_destination LIKE '%{$search_text}' OR hs.search_destination LIKE '{$search_text}%' OR hs.search_destination = '{$search_text}')";
                    $this->db->where($or_where);
                } else if((isset($post['search_text']) && !empty($post['search_text'])) && (isset($post['search_index']) && !empty($post['search_index'])) && $flag == 0) {
                    if(isset($post['search_type']) && $post['search_type'] == 'hotel') {
                        $this->db->where('hs.EANHotelID', $post['search_index']);
                    } else if($post['search_type'] == 'country') {
                        $this->db->where('hs.search_destination', $post['search_index']);
                    }
                }

                if(isset($post['country_id']) && empty($post['country_id'])) {
                    unset($post['country_id']);
                }
                if(isset($post['city']) && empty($post['city'])) {
                    unset($post['city']);
                }

                //pr($post) ;
                //pr($this->data['searchDestinationCities']) ;

                if(isset($post['search_text']) && !empty($post['search_text']) && isset($post['search_index']) && !empty($post['search_index']) && $flag == 0) {
                    $query = $this->app_model->find('hotels_select hs', 'all', array('hs.*'), array('hs.is_active' => 1), array(
                            array('select_hotel_styles shs', 'shs.EANHotelID=hs.EANHotelID', 'LEFT'),
                            array('countries c', 'hs.Country=c.iso2', 'LEFT')
                            //array('cities city', 'c.country_id=city.country_id', 'LEFT'),
                        )
                    );
                } else if(isset($post['search_text']) && !empty($post['search_text']) && $flag == 0) {
                    $query = $this->app_model->find('hotels_select hs', 'all', array('hs.*'), array('hs.is_active' => 1), array(
                            array('select_hotel_styles shs', 'shs.EANHotelID=hs.EANHotelID', 'LEFT'),
                            array('countries c', 'hs.Country=c.iso2', 'LEFT')
                            //array('cities city', 'c.country_id=city.country_id', 'LEFT'),
                        )
                    );
                } else if(( (isset($post['country_id']) && empty($post['country_id'])) || (isset($post['city']) && empty($post['city']))) && (empty($post['wellness_style']) && empty($post['categories']))) {
                    $query = array();
                } else if(( (!isset($post['country_id']) ) && (!isset($post['city']) )) && (!isset($post['wellness_style']) && !isset($post['categories']))) {

                    $query = array();
                } else {
                    $query = $this->app_model->find('hotels_select hs', 'all', array('hs.*'), array('hs.is_active' => 1, 'hs.search_destination !=' => ''), array(array('select_hotel_styles shs', 'shs.EANHotelID=hs.EANHotelID', 'LEFT')));
                }

                if(!isset($post['rooms'])) {
                    $post['rooms'] = $this->data['search']['rooms'];
                }
                if(isset($query) && !empty($query)) {
                    foreach($query as $content) {
                        $hotelsData[$content['EANHotelID']] = $content;
                        $this->db->order_by('images.is_main', 'DESC');
                        $images = $this->app_model->find('hotel_images images', 'all', 'images.*', array('images.EANHotelID' => $content['EANHotelID']));
                        $hotelsData[$content['EANHotelID']]['images'] = $images;
                    }
                    $hotelsId = array_keys($hotelsData);

                    foreach($hotelsData as $hotelID => $hotelData) {
                        $hotelsData[$hotelID]['activities'] = $this->get_hotels_style($hotelID);
                    }

                    // We probably have all we need in $hotelsData
                    //pr($hotelsData);

//                $hotelsId = implode(',', $hotelsId);
                    // Hotels ID now contains a list of IDs comma separated
                    //pr($hotelsId);
// Using these IDs we should now source the data from the database...

//                $foundProperties = $this->app_model->find('hotels_select hs', 'all', array($hotelsId, )

                    // The following is a useless call to EAN
//                $room = isset($post['rooms']) ? $post['rooms'] : '';
//                $call_api = $this->call_ean_api('list', selected_currency, $hotelsId, 'en_US', $post['date1'], $post['date2'], $room, $post);
//                pr($call_api);
//                $decode = json_decode($call_api, true);
//                // pr($decode) ;
//                $this->data['hotellist'][] = $decode;
//                $content = $this->recursive_fetch_ean($decode);
                    /* pr($decode['HotelListResponse']['cacheKey']) ;
                      pr($decode['HotelListResponse']['cacheLocation']) ; */

                    // This will never be populated because it relies on a call to EAN
//                if(isset($content) && !empty($content)) {
//                    foreach($content as $decode) {
//                        //pr($decode['HotelListResponse']['HotelList']) ;
//                        if(isset($decode['HotelListResponse']['HotelList']['HotelSummary']) && !empty($decode['HotelListResponse']['HotelList']['HotelSummary'])) {
//                            $check_hotel_length = $decode['HotelListResponse']['HotelList']['@size'];
//                            if($check_hotel_length <= 1) {
//                                $hotel = $decode['HotelListResponse']['HotelList']['HotelSummary'];
//
//                                if(isset($hotelsData[$hotel['hotelId']]) && !empty($hotelsData[$hotel['hotelId']])) {
//                                    $expedia[$hotel['hotelId']] = $hotelsData[$hotel['hotelId']];
//                                    $expedia[$hotel['hotelId']]['expedia'] = $hotel;
//                                    $expedia[$hotel['hotelId']]['is_wish'] = $this->is_check_hotels_and_deals_wishlist($hotel['hotelId'], 'hotels');
//                                    $expedia[$hotel['hotelId']]['activities'] = $this->get_hotels_style($hotel['hotelId']);
//                                    if(isset($post['price_range']) && !empty($post['price_range']) && (isset($post['price_filter_change']) && $post['price_filter_change'] == 1 )) {
//                                        $amt = isset($hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate']) ? $hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate'] : 0;
//                                        $price_split = explode(',', $post['price_range']);
//                                        if($price_split[0] <= $amt && $price_split[1] >= $amt) {
//
//                                            $sortByPrice[$hotel['hotelId']] = $amt;
//                                        }
//                                    } else {
//                                        $sortByPrice[$hotel['hotelId']] = isset($hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate']) ? $hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate'] : 0;
//                                    }
//                                }
//                            } else {
//                                //pr($decode['HotelListResponse']['HotelList']) ;
//                                foreach($decode['HotelListResponse']['HotelList']['HotelSummary'] as $hotel) {
//                                    if(isset($hotelsData[$hotel['hotelId']]) && !empty($hotelsData[$hotel['hotelId']])) {
//                                        $expedia[$hotel['hotelId']] = $hotelsData[$hotel['hotelId']];
//                                        $expedia[$hotel['hotelId']]['expedia'] = $hotel;
//                                        $expedia[$hotel['hotelId']]['is_wish'] = $this->is_check_hotels_and_deals_wishlist($hotel['hotelId'], 'hotels');
//                                        $expedia[$hotel['hotelId']]['activities'] = $this->get_hotels_style($hotel['hotelId']);
//                                        //$sortByPrice[$hotel['hotelId']] = isset($hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate']) ? $hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate'] : 0;
//                                        if(isset($post['price_range']) && !empty($post['price_range']) && (isset($post['price_filter_change']) && $post['price_filter_change'] == 1 )) {
//                                            $amt = isset($hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate']) ? $hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate'] : 0;
//                                            $price_split = explode(',', $post['price_range']);
//                                            if($price_split[0] <= $amt && $price_split[1] >= $amt) {
//
//                                                $sortByPrice[$hotel['hotelId']] = $amt;
//                                            }
//                                        } else {
//                                            $sortByPrice[$hotel['hotelId']] = isset($hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate']) ? $hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate'] : 0;
//                                        }
//                                    }
//                                }
//                            }
//                        }
//                    }
//                }
                }
            }
            $this->data['startPrice'] = 0;
            $this->data['endPrice'] = 0;
            //pr($post);
            if(isset($sortByPrice) && !empty($sortByPrice)) {
                asort($sortByPrice);
                if(isset($post['sort_by']) && !empty($post['sort_by']) && $post['sort_by'] == 2) {
                    arsort($sortByPrice);
                }
            }
            if(isset($expedia) && !empty($expedia)) {
                $total = count($sortByPrice);
                $this->data['totalPages'] = ceil($total / self::$per_page);

                $this->data['startPrice'] = current(($sortByPrice));
                $this->data['endPrice'] = end($sortByPrice);
            }
            $this->session->set_userdata('expediaData', $expedia);
            //$this->session->set_userdata('searchData',array()) ;


            $this->session->set_userdata('sortByPrice', $sortByPrice);
            $this->data['expedia'] = $expedia;
            if(!empty($expedia) && count($expedia) > self::$per_page) {
                $this->data['expediaData'] = array_slice($sortByPrice, 0, self::$per_page, true);
            } else {
                $this->data['expediaData'] = $sortByPrice;
            }
            if($this->session->has_userdata('room_filter')) {
                $room_filter = $this->session->userdata('room_filter');
                $this->data['room_filter'] = $room_filter;
            }
            if(!isset($post['sort_by'])) {
                $post['sort_by'] = 1;
            }
            if(isset($post['search_text']) && !empty($post['search_text'])) {
                unset($post['continent_id']);
            }

            if(isset($post['country_id']) && in_array('B1', $post['country_id'])) {
                unset($post['country_id']['999']);
                unset($post['country_id']['998']);
            }

            if(isset($post['country_id']) && in_array('C1', $post['country_id'])) {
                unset($post['country_id']['991']);
                unset($post['country_id']['992']);
                unset($post['country_id']['993']);
                unset($post['country_id']['994']);
                unset($post['country_id']['995']);
                unset($post['country_id']['996']);
            }
            $this->data['post'] = $post;

            $this->session->set_userdata('searchData', $post);
            //unset($post) ;
            //unset($_POST) ;
            //pr($this->data['post']) ;
            //pr($this->data['post']) ;
            //added by Rakesh to correct price filter start
            if(isset($post['price_range']) && (isset($post['price_filter_change']) && $post['price_filter_change'] == 1 )) {
                if($flag == 0 && isset($post['sort_by']) && $post['sort_by'] == 2) {
                } else {
                    $price_split = explode(',', $post['price_range']);
                    $this->data['startPrice'] = ($price_split[0] < $this->data['startPrice']) ? $price_split[0] : $this->data['startPrice'] - 1;
                    $this->data['endPrice'] = ($price_split[1] > $this->data['endPrice']) ? $price_split[1] : $this->data['endPrice'] + 1;
                }
            }
            //added by Rakesh to correct price filter end

            if($this->data['endPrice'] > $this->data['startPrice']) {
                $this->data['startPrice'] = floor($this->data['startPrice']) - 1;
                $this->data['endPrice'] = ceil($this->data['endPrice']) + 1;
            }
            if($this->data['startPrice'] > $this->data['endPrice']) {
                $this->data['startPrice'] = ceil($this->data['startPrice']) + 1;
                $this->data['endPrice'] = floor($this->data['endPrice']) - 1;
            }

            //added by Rakesh to correct price filter start
            if(isset($post['max_range']) && (isset($post['price_filter_change']) && $post['price_filter_change'] == 1 )) {
                $this->data['price_range_start'] = $post['min_range'];
                $this->data['price_range_end'] = $post['max_range'];
            } else {
                if($this->data['startPrice'] > $this->data['endPrice']) {
                    $this->data['price_range_start'] = $this->data['endPrice'];
                    $this->data['price_range_end'] = $this->data['startPrice'];
                } else {
                    $this->data['price_range_start'] = $this->data['startPrice'];
                    $this->data['price_range_end'] = $this->data['endPrice'];
                }
            }
            //added by Rakesh to correct price filter end
            //pr($this->data);

            if($this->input->is_ajax_request()) {
                $flag = 1;
            }
            if($flag == 0) {
                if(isset($post['search_type']) && !empty($post['search_type']) && $post['search_type'] == 'hotel' && !empty($post['search_index']) && count($expedia) == 1) {
                    redirect(base_url() . 'hotel_detail/' . $post['search_index']);
                } else {
                    $meta_title = COMPANY_NAME . " - " . COMPANY_TAGLINE;
                    $meta_description = COMPANY_NAME . " - " . COMPANY_TAGLINE;
                    if(isset($get_data) && !empty($get_data)) {
                        $post['get_data'] = $get_data ;
                        //pr($post['get_data']) ;
                    }

                    if(isset($post['continent_id']) && !empty($post['continent_id'])) {
                        $post['continent_id'] = array_flip($post['continent_id']) ;
                    }

                    if((isset($post['get_data'][0]) && isset($post['get_data'][2]) && isset($post['get_data'][4]) ) && $post['get_data'][0]=='ct' && $post['get_data'][2]=='s' && $post['get_data'][4]=='at') {
                        $con_country_data = array_flip($post['con_country_data']);
                        $ct_name = key($con_country_data) ;
                        //$chk_meta = get_landing_wellness_data_all($post['get_data'][1],$post['get_data'][3],$ct_name);

                        $treatment_Cat = getCategoryTreatmentCatName($post['get_data']['5']);
                        $treatment_list = get_activities_per_Category_data($post['get_data']['5']);
                        $treatment_list_limit = array_slice($treatment_list,0,3) ;
                        $activities = implode(', ',$treatment_list_limit);
                        $wellness =  $this->get_wellness_data ();
                        $wellness_name = $wellness['wellness_style'][$post['get_data']['3']] ;

                        $meta_title = "Book Online Resorts {$wellness_name} & {$treatment_Cat[$post['get_data']['5']]} In  {$ct_name} | In This Life Wellness Travel." ;
                        $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find, Book & then {$activities} at a {$wellness_name} in {$ct_name}." ;
                    } else if((isset($post['get_data'][0]) && isset($post['get_data'][2]) && isset($post['get_data'][4]) ) && $post['get_data'][0]=='c' && $post['get_data'][2]=='s' && $post['get_data'][4]=='a') {
                        $ct_name = $post['country_id'][0] ;
                        $chk_meta = get_landing_wellness_data_all($post['get_data'][3],$post['get_data'][5],$ct_name);
                        if(!empty($chk_meta)) {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        } else {
                            $activity_name = get_activities_data($post['get_data'][5]) ;
                            $treatment_Cat = getCategoryTreatmentCat($post['get_data'][5]);
                            $treatment_list = get_activities_per_Category_data($treatment_Cat[0]);
                            unset($treatment_list[$post['categories'][0]]) ;
                            $activities = $activity_name['name'] ;
                            $arg = end($treatment_list) ;
                            // $activities.=', '.$arg ;
                            $wellness =  $this->get_wellness_data ();
                            $wellness_name = $wellness['wellness_style'][$post['get_data']['3']] ;

                            $meta_title = "Book Online Resorts {$wellness_name} & {$activities} In  {$ct_name} | In This Life Wellness Travel." ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find, Book & then {$activities} at a {$wellness_name} in {$ct_name}." ;
                        }
                    }  else if((isset($post['get_data'][0]) && isset($post['get_data'][2])) && $post['get_data'][0]=='c' && $post['get_data'][2]=='a') {
                        $ct_name = $post['country_id'][0] ;
                        $chk_meta = get_landing_activities_new_data($post['get_data'][3],$ct_name);
                        if(!empty($chk_meta)) {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        } else {

                            $activity_name = get_activities_data($post['get_data'][3]) ;
                            $treatment_Cat = getCategoryTreatmentCat($post['get_data'][3]);
                            $treatment_list = get_activities_per_Category_data($treatment_Cat[0]);
                            unset($treatment_list[$post['categories'][0]]) ;
                            $activities = $activity_name['name'] ;
                            $arg = end($treatment_list) ;
                            $activities.=', '.$arg ;
                            $meta_title = "Book Online {$activities} Hotels & Resorts, Spas & Retreats In  {$ct_name}| In This Life Wellness Travel" ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness & {$activities} & more in {$ct_name}." ;
                        }
                    } else if((isset($post['get_data'][0]) && isset($post['get_data'][2])) && $post['get_data'][0]=='z' && $post['get_data'][2]=='a') {

                        $ct_name = $post['city'][0] ;
                        $chk_meta = get_landing_activities_new_data($post['get_data'][3],$ct_name);
                        if(!empty($chk_meta)) {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        } else {

                            $activity_name = get_activities_data($post['get_data'][3]) ;
                            $treatment_Cat = getCategoryTreatmentCat($post['get_data'][3]);
                            $treatment_list = get_activities_per_Category_data($treatment_Cat[0]);
                            unset($treatment_list[$post['categories'][0]]) ;
                            $activities = $activity_name['name'] ;
                            $arg = end($treatment_list) ;
                            $activities.=', '.$arg ;

                            $meta_title = "Book Online {$activities} Hotels & Resorts, Spas & Retreats In  {$ct_name}| In This Life Wellness Travel" ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness & {$activities} & more in {$ct_name}." ;
                        }
                    } else if((isset($post['get_data'][0]) && isset($post['get_data'][2])) && $post['get_data'][0]=='ct' && $post['get_data'][2]=='a') {
                        $con_country_data = array_flip($post['con_country_data']);
                        $ct_name = key($con_country_data) ;
                        $chk_meta = get_landing_activities_new_data($post['get_data'][3],$ct_name);
                        if(!empty($chk_meta)) {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        } else {

                            $activity_name = get_activities_data($post['get_data'][3]) ;
                            $treatment_Cat = getCategoryTreatmentCat($post['get_data'][3]);
                            $treatment_list = get_activities_per_Category_data($treatment_Cat[0]);
                            unset($treatment_list[$post['categories'][0]]) ;
                            $activities = $activity_name['name'] ;
                            $arg = end($treatment_list) ;
                            $activities.=', '.$arg ;

                            $meta_title = "Book Online {$activities} Hotels & Resorts, Spas & Retreats In  {$ct_name}| In This Life Wellness Travel" ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness & {$activities} & more in {$ct_name}." ;
                        }
                    } else if((isset($post['get_data'][0]) && isset($post['get_data'][2])) && $post['get_data'][0]=='c' && $post['get_data'][2]=='s') {
                        $ct_name = $post['country_id'][0] ;
                        $chk_meta = get_landing_style_data($post['get_data'][3],$ct_name);
                        if(!empty($chk_meta)) {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        } else {
                            $wellness =  $this->get_wellness_data ();
                            $wellness_name = $wellness['wellness_style'][$post['get_data']['3']] ;
                            $meta_title = "Book Online {$wellness_name} Hotels & Resorts, Spas & Retreats In  {$ct_name} | In This Life Wellness Travel" ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness & {$wellness_name} Hotel, Spa, Resort, Retreat, Yoga retreat or Meditation Retreat & more in {$ct_name}." ;
                        }
                    } else if((isset($post['get_data'][0]) && isset($post['get_data'][2])) && $post['get_data'][0]=='ct' && $post['get_data'][2]=='s') {
                        $con_country_data = array_flip($post['con_country_data']);
                        $ct_name = key($con_country_data) ;
                        $chk_meta = get_landing_style_data($post['get_data'][3],$ct_name);
                        if(!empty($chk_meta)) {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        } else {
                            $wellness =  $this->get_wellness_data ();
                            $wellness_name = $wellness['wellness_style'][$post['get_data']['3']] ;
                            $meta_title = "Book Online {$wellness_name} Hotels & Resorts, Spas & Retreats In  {$ct_name} | In This Life Wellness Travel" ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your {$wellness_name} & ECO Hotel, Spa, Resort, Retreat, Yoga retreat or Meditation Retreat & more in {$ct_name}." ;
                        }
                    } else if((isset($post['get_data'][0]) && isset($post['get_data'][2])) && $post['get_data'][0]=='z' && $post['get_data'][2]=='s') {
                        $ct_name = $post['city'][0] ;
                        $chk_meta = get_landing_style_data($post['get_data'][3],$ct_name);
                        if(!empty($chk_meta)) {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        } else {
                            $wellness =  $this->get_wellness_data ();
                            $wellness_name = $wellness['wellness_style'][$post['get_data']['3']] ;
                            $meta_title = "Book Online {$wellness_name} Hotels & Resorts, Spas & Retreats In  {$ct_name} | In This Life Wellness Travel" ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your {$wellness_name} & ECO Hotel, Spa, Resort, Retreat, Yoga retreat or Meditation Retreat & more in {$ct_name}." ;
                        }
                    } else if((isset($post['get_data'][0]) && isset($post['get_data'][2]) ) && $post['get_data'][0]=='ct' && $post['get_data'][2]=='at') {
                        $con_country_data = array_flip($post['con_country_data']);
                        $ct_name = key($con_country_data) ;

                        $treatment_Cat = getCategoryTreatmentCatName($post['get_data']['3']);
                        $treatment_list = get_activities_per_Category_data($post['get_data']['3']);
                        $treatment_list_limit = array_slice($treatment_list,0,3) ;
                        $activities = implode(', ',$treatment_list_limit);
                        $meta_title = "Book Online Wellness Hotels & Retreats in {$ct_name} | {$treatment_Cat[$post['get_data']['3']]} | In This Life Wellness Travel" ;
                        $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find, Book & then {$activities} at a Wellness Hotel, Health Spa, Luxury Wellness Spa or Eco Resort/Retreat in {$ct_name}." ;
                    } else if(isset($post['continent_id']) && count($post['continent_id']) == 1 && isset($post['wellness_style']) && count($post['wellness_style']) == 1 && isset($post['get_data'])) {
                        $con_country_data = array_flip($post['con_country_data']);
                        if(count($con_country_data) == 1) {
                            $continentList = get_continents();
                            $continent_name = strtoupper($continentList[$post['continent_id'][0]]);
                            $meta_title = "Book Online Wellness Hotels, Spas, Resorts & Retreats {$continent_name} | In This Life Wellness Travel";
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness Hotel, Spa, Resort & Retreat, Yoga retreat, Meditation Retreat & more in {$continent_name}.";
                        }
                    } else if(isset($post['continent_id']) && count($post['continent_id']) == 1 && isset($post['get_data'])) {
                        $continentList = get_continents();
                        $con_country_data = array_flip($post['con_country_data']);
                        $destinationID = reset($post['continent_id']) ;
                        if(isset($destinationID) && !empty($destinationID)) {
                            $destinationName = $continentList[$destinationID] ;
                            $chk_meta  = get_landing_destination_data($destinationName);
                        } else if($post['get_data'][0]=='ct') {
                            $destinationName = $continentList[$post['get_data'][1]] ;
                            $chk_meta  = get_landing_destination_data($destinationName);
                        }

                        if(!empty($chk_meta)) {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        } else {
                            if(count($con_country_data) == 1) {
                                // pr($continentList) ;
                                if(isset($continentList[$post['continent_id'][0]])) {
                                    $continent_name = strtoupper($continentList[$post['continent_id'][0]]);
                                } else {
                                    $continent_name = strtoupper($continentList[$post['get_data'][1]]);
                                }
                                $meta_title = "Book Online Wellness Hotels, Spas, Resorts & Retreats {$continent_name} | In This Life Wellness Travel";
                                $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness Hotel, Spa, Resort & Retreat, Yoga retreat, Meditation Retreat & more in {$continent_name}.";
                            }
                        }

                        $continentsData = $this->get_continents_data();

                        $continentId = $post['get_data'][0];
                        // Nb: for some reason on teh second load of the search page, the get_data post value changes to being an array containing the saerch type (e.g. "ct") and the ID, so we look at the second element in the array if that is the case
                        if(count($post['get_data']) == 2) {
                            $continentId = $post['get_data'][1];
                        }

                        $continent = multidimensional_array_search_by_key($continentId, 'id', $continentsData);
                        $this->data['intro_content_title'] = $continent['name'];
                        $this->data['intro_content_text'] = $continent['short_description'];
                    } else if(isset($post['country_id']) && count($post['country_id']) == 1 && isset($post['get_data'])) {

                        $country_name = strtoupper($post['country_id'][0]);
                        $chk_meta  = get_landing_destination_data($country_name);
                        if(!empty($chk_meta)) {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        } else {
                            $meta_title = "Book Online Wellness Hotels, Spas, Resorts & Retreats {$country_name} | In This Life Wellness Travel";
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness Hotel, Spa, Resort & Retreat, Yoga retreat, Meditation Retreat & more in {$country_name}.";
                        }

                        $destinationsData = $this->get_destination_data();

                        $countryId = $post['get_data'][0];
                        // Nb: for some reason on teh second load of the search page, the get_data post value changes to being an array containing the saerch type (e.g. "c") and the ID, so we look at the second element in the array if that is the case
                        if(count($post['get_data']) == 2) {
                            $countryId = $post['get_data'][1];
                        }

                        $country = multidimensional_array_search_by_key($countryId, 'country_id', $destinationsData);
                        $this->data['intro_content_title'] = $country_name;
                        $this->data['intro_content_text'] = $country['short_description'];

                    } else if(isset($post['city']) && count($post['city']) == 1 && isset($post['get_data'])) {
                        $city_name = strtoupper($post['city'][0]);
                        $chk_meta  = get_landing_destination_data($city_name);
                        if(!empty($chk_meta)) {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        } else {
                            $meta_title = "Book Online Wellness Hotels, Spas, Resorts & Retreats {$city_name} | In This Life Wellness Travel";
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness Hotel, Spa, Resort & Retreat, Yoga retreat, Meditation Retreat & more in {$city_name}.";
                        }

                        $destinationsData = $this->get_destination_data();

                        $cityId = $post['get_data'][0];
                        // Nb: for some reason on teh second load of the search page, the get_data post value changes to being an array containing the saerch type (e.g. "z") and the ID, so we look at the second element in the array if that is the case
                        if(count($post['get_data']) == 2) {
                            $cityId = $post['get_data'][1];
                        }

                        $city = multidimensional_array_search_by_key($cityId, 'city_id', $destinationsData);
                        $this->data['intro_content_title'] = $city_name;
                        $this->data['intro_content_text'] = $city['short_description'];
                    } else if(isset($post['wellness_style']) && count($post['wellness_style']) == 1 && isset($post['get_data'])) {
                        $wellnessData = $this->get_wellness_data();
                        $wellness_name = strtoupper($wellnessData['wellness_style'][$post['wellness_style'][0]]);
                        $chk_meta  = get_landing_wellness_data($post['wellness_style'][0]);
                        if(!empty($chk_meta)) {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        } else {
                            $meta_title = "Book Online {$wellness_name} Hotels & Resorts, Spas & Retreats | In This Life Wellness Travel";
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness & {$wellness_name} Hotel, Spa, Resort, Retreat, Yoga retreat or Meditation Retreat & more.";
                        }

                        $this->data['intro_content_title'] = $wellnessData['wellness_style_detail'][$post['wellness_style'][0]]['name'];
                        $this->data['intro_content_text'] = $wellnessData['wellness_style_detail'][$post['wellness_style'][0]]['short_description'];
                    } else if(isset($post['categories'][0]) && !empty($post['categories'][0]) && count($post['categories'])==1 && isset($post['get_data'])) {
                        $chk_meta =  get_landing_activities_data($post['categories'][0]) ;
                        if(!empty($chk_meta)) {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        } else {
                            $activity_name = get_activities_data($post['categories'][0]) ;
                            $treatment_Cat = getCategoryTreatmentCat($post['categories'][0]);
                            $treatment_list = get_activities_per_Category_data($treatment_Cat[0]);
                            unset($treatment_list[$post['categories'][0]]) ;
                            $activities = $activity_name['name'] ;
                            $arg = end($treatment_list) ;
                            $activities.=', '.$arg ;
                            $meta_title = "Book Online {$activity_name['name']}, Resorts, Hotels & Spas | In This Life Wellness Travel" ;
                            $meta_description =  "World’s only Wellness Travel Company with live availability. Search, Find and book your Wellness Hotel, Spa & Resort, {$activities} and more.";
                        }

                        $activityDetails = getCategoryTreatmentCatDetails($post['categories'][0]);
                        $this->data['intro_content_title'] =  $activityDetails['name'];
                        $this->data['intro_content_text'] = $activityDetails['short_description'];
                    } else if(isset($post['get_data'][0]) && !empty($post['get_data'][0]) && $post['get_data']['0']=='at') {
                        $treatment_Cat = getCategoryTreatmentCatName($post['get_data']['1']);
                        $treatment_list = get_activities_per_Category_data($post['get_data']['1']);
                        $treatment_list_limit = array_slice($treatment_list,0,3) ;
                        $activities = implode(', ',$treatment_list_limit);
                        $meta_title = "Book Online {$treatment_Cat[$post['get_data']['1']]}, Resorts, Hotels & Spas | In This Life Wellness Travel" ;
                        $meta_description =  "World’s only Wellness Travel Company with live availability. Search, Find and book your Wellness Hotel, Spa & Resort, {$activities} and more.";
                        $activityDetails = getCategoryTreatmentCatDetails($post['get_data']['1']);
                        $this->data['intro_content_title'] =  $activityDetails['name'];
                        $this->data['intro_content_text'] = $activityDetails['short_description'];
                    }
                    $meta_keyword = COMPANY_NAME . " - " . COMPANY_TAGLINE ;

                    if(isset($chk_meta) && !empty($chk_meta)) {
                        $this->data['meta_bottom'] =  $chk_meta['description'] ;
                        $meta_keyword = $chk_meta['meta_keyword'] ;
                    }

                    // Populates expediaData with details from our database
                    $this->data['expediaData'] = $hotelsData;
                    $this->data['expedia'] = $hotelsData;

                    $this->template->write('title', $meta_title);
                    $this->template->write('meta_description', $meta_description);
                    $this->template->write('meta_keywords', $meta_keyword);

                    $this->template->write_view('content', 'front/home/industrysearch', $this->data);
                    $this->template->render();
                }
            } else {
                $total = isset($this->data['totalPages']) ? $this->data['totalPages'] : 0;

                $this->data['expediaData'] = $hotelsData;
                $this->data['expedia'] = $hotelsData;
                $html = $this->load->view('front/home/common/industryproduct', $this->data, true);

                $response = array('html' => $html, 'page' => 2, 'total' => $total, 'startPrice' => ceil($this->data['startPrice']), 'endPrice' => ceil($this->data['endPrice']), 'price_range_start' => ceil($this->data['price_range_start']), 'price_range_end' => ceil($this->data['price_range_end']));
                echo json_encode($response);
                die;
            }
        } else {

            $temp_data = $params;

            $this->data['footer_active_menu'] = $temp_data;
            $expedia = array();
            $sortByPrice = array();
            $hotelsData = array();
            $this->data['con_country_data'] = array();
            $this->data['categories_activities'] = array();
            $this->data['searchCities'] = $this->get_search_destination_without_country();
            if(isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO']!='search')
            {
                //echo $_SERVER['PATH_INFO'] ; die ;
                //pr($_SERVER) ;
                $this->data['redirectLink'] = $_SERVER['PATH_INFO'] ;
            }
            if(isset($params) && $params != "")
            {
                if($params3=='')
                {
                    $last = $this->uri->total_segments();
                    $params3 = $this->uri->segment($last);
                    $get_data = explode("-", $params3);
                }
                else
                {
                    $get_data = explode("-", $params);
                }



                if(isset($get_data) && $get_data[0] == "z")
                {
                    $cityID = $get_data['1'];
                    $cityName = isset($this->data['searchCities'][$cityID]) ? $this->data['searchCities'][$cityID] : $cityID;
                    $city[] = $cityName;
                }

                if(isset($get_data) && $get_data[0] == "c")
                {
                    $checkKey = explode(',', $get_data['1']);


                    if(strstr($get_data['1'], '90909') && count($checkKey) == 1)
                    {
                        $get_data[0] = 'z';
                        $city = str_replace('90909', '', $get_data['1']);
                    }
                    else if(strstr($get_data['1'], '90909') && count($checkKey) > 1)
                    {
                        $match = array();
                        foreach($checkKey as $content)
                        {
                            if(substr($content, 0, 5) == '90909')
                            {
                                $matches = substr($content, 5);
                                $city[] = isset($this->data['searchCities'][$matches]) ? $this->data['searchCities'][$matches] : $matches;
                                $get_data['1'] = str_replace('90909' . $matches . ',', '', $get_data['1']);
                                $get_data['1'] = str_replace('90909' . $matches, '', $get_data['1']);
                            }
                        }
                    }
                }
                $wellness_style = "";
                $categories = "";
                if(isset($get_data) && $get_data[0] == "c")
                {
                    $con_ids = explode(',', $get_data[1]);
                    $this->db->where_in("c.iso2", $con_ids);
                    $country_data1 = $this->app_model->find('countries c', 'all', array('c.country_name'), array('c.is_active' => 1,));

                    if(empty($country_data1))
                    {
                        $this->db->where_in("c.country_id", $con_ids);
                        $country_data1 = $this->app_model->find('countries c', 'all', array('c.iso2', 'c.country_name'), array('c.is_active' => 1,));
                    }

                    foreach($country_data1 as $ids => $value)
                    {
                        $country_data[] = $value['country_name'];
                    }
                    if(isset($country_data))
                    {
                        $country_code = $country_data;
                    }
                    else
                    {
                        $country_code = "";
                    }

                }

                if(isset($get_data[0]) && $get_data[0] == "ct")
                {
                    $continent_id = $get_data[1] ;
                    $list =  get_country_base_on_continent_id($get_data[1]) ;
                    if(isset($list['country']))
                    {
                        $con_ids = array_values($list['country']) ;
                    }


                    foreach($list['country'] as $name => $id)
                    {
                        $country_data[] = $name ;
                    }
                    if(isset($country_data))
                    {
                        $country_code = $country_data;
                    }
                    else
                    {
                        $country_code = "";
                    }

                    if(isset($list['cities']))
                    {

                        $city = array_keys($list['cities']) ;
                    }
                }
                if(isset($get_data) && ($get_data[0] == "s" || (isset($get_data[2]) && $get_data[2] == "s" )))
                {
                    if((isset($get_data[2]) && $get_data[2] == "s"))
                    {
                        $wellness_style = $get_data[3];

                        if(isset($get_data[3]))
                        {
                            $wellness_style = $get_data[3];
                        }
                    }
                    else
                    {
                        $wellness_style = $get_data[1];

                        if(isset($get_data[1]))
                        {
                            $wellness_style = $get_data[1];
                        }
                    }
                }
                if(isset($get_data[0]) && $get_data[0] == "a")
                {
                    //  categories
                    if(isset($get_data[1]))
                    {
                        $categories = $get_data[1];
                    }
                    else
                    {
                        $categories = "";
                    }
                }
                if(isset($get_data[0]) && $get_data[0] == "z")
                {
                    //  categories
                    if(isset($get_data[1]))
                    {
                        $city = $get_data[1];
                    }
                }

                if(isset($get_data[2]) && $get_data[2] == "a")
                {
                    //  categories
                    if(isset($get_data[3]))
                    {
                        $categories = $get_data[3];
                    }
                    else
                    {
                        $categories = "";
                    }
                }

                //logic992
                if(isset($get_data[4]) && $get_data[4] == "a")
                {
                    //  categories
                    if(isset($get_data[5]))
                    {
                        $categories = $get_data[5];
                    }
                    else
                    {
                        $categories = "";
                    }
                }
                if(isset($get_data[2]) && $get_data[2] == "at")
                {
                    //  categories


                    if(isset($get_data[3]))
                    {
                        //$post['parent_cat'] = $get_data[3] ;
                        $categories = $this->getCategoryTreatmentStyleId($get_data[3]);

                    }
                    else
                    {
                        $categories = "";
                    }
                }

                if(isset($get_data[0]) && $get_data[0] == "at")
                {
                    //  categories


                    if(isset($get_data[1]))
                    {
                        //$post['parent_cat'] = $get_data[3] ;
                        $categories = $this->getCategoryTreatmentStyleId($get_data[1]);
                    }
                    else
                    {
                        $categories = "";
                    }
                }
                if(isset($get_data[4]) && $get_data[4] == "at")
                {
                    //  categories


                    if(isset($get_data[5]))
                    {
                        //$post['parent_cat'] = $get_data[3] ;
                        $categories = $this->getCategoryTreatmentStyleId($get_data[5]);
                    }
                    else
                    {
                        $categories = "";
                    }
                }

            }
            $flag = 0;
            if($this->input->is_ajax_request())
            {
                $flag = 1;
            }


            $hotelsData = array();
            $post = $this->input->post();

            if(isset($city) && !empty($city))
            {
                $post['city'] = $city ;
            }

            //pr($post) ;

            $split_for_date = explode('-d', $params2);
            if(isset($split_for_date[1]) && !empty($split_for_date[1]))
            {
                $post['datefilter'] = str_replace(':', ' - ', $split_for_date[1]);
                $datefilter = str_replace(':', ' - ', $split_for_date[1]);
            }



            if(isset($post['search_data']) && isset($post['datefilterpop']))
            {
                $get_data = explode("-", $params2);

                if(isset($get_data) && $get_data[0] == "c")
                {
                    $con_ids = explode(',', $get_data[1]);
                    $this->db->where_in("c.country_id", $con_ids);
                    $country_data1 = $this->app_model->find('countries c', 'all', array('c.iso2'), array('c.is_active' => 1));
                    foreach($country_data1 as $ids => $value)
                    {
                        $country_data[] = $value['iso2'];
                    }
                    if(isset($country_data))
                    {
                        $country_code = $country_data;
                    }
                    else
                    {
                        $country_code = "";
                    }
                }
                if(isset($get_data) && $get_data[0] == "s")
                {
                    // wellness_style =  $get_data[1];
                    if(isset($get_data[1]))
                    {
                        $wellness_style = explode(',', $get_data[1]);
                    }
                    else
                    {
                        $wellness_style = "";
                    }

                }
                if(isset($get_data) && $get_data[0] == "a")
                {
                    //  categories
                    if(isset($get_data[1]))
                    {
                        $categories = $get_data[1];
                    }
                    else
                    {
                        $categories = "";
                    }
                }



                if(isset($post['datefilterpop']))
                {
                    $datefilter = $post['datefilterpop'];
                }
            }

            if(isset($continent_id) && !empty($continent_id))
            {
                $post['continent_id'][] = $continent_id ;
            }

            if(isset($post['datefilter']) && !empty($post['datefilter']))
            {
                $datefilter = $post['datefilter'] ;
            }

            if(isset($country_code))
            {
                $post['country_id'] = $country_code;
                $post['datefilter'] = $datefilter;
            }



            if(isset($wellness_style) && !empty($wellness_style))
            {
                if(!is_array($wellness_style))
                {
                    $wellness_style = explode(',', $wellness_style);
                }
                $post['wellness_style'] = $wellness_style;
            }
            if(isset($categories) && !empty($categories))
            {
                if(is_array($categories))
                {
                    $post['categories'] = $categories;
                }
                else
                {
                    $post['categories'] = explode(',', $categories);
                }
            }
            /* if(empty($post))
              {
              $expedia = $this->session->userdata('expediaData');
              $sortByPrice = $this->session->userdata('sortByPrice');
              }
             */

            $this->data['search_destination'] = $this->get_search_destination_without_country();
            $this->data['search_destination']+= $this->get_search_destination_without_continent();
            //pr($this->data['search_destination']) ;


            if($this->session->has_userdata('searchData'))
            {
                $searchData = $this->session->userdata('searchData');
            }

            if((!isset($post) || empty($post)) && (isset($searchData) && !empty($searchData)) && $flag == 0)
            {
                //need to check for low to high & hight to low issue
                $post = $this->session->userdata('searchData');
                if($this->session->has_userdata('expediaData') && !empty($post))
                {
                    $expedia = $this->session->userdata('expediaData');
                    $sortByPrice = $this->session->userdata('sortByPrice');
                }
            }
            /*
            if(!isset($post['datefilter']))
            {
                $post['datefilter'] = get_date_range() ;
                $datefilter = get_date_range() ;
            }
            */
            //pr($post) ;

            if(isset($post) && !empty($post))
            {

                if(isset($post['currency_selector']) && !empty($post['currency_selector']))
                {
                    if($this->session->has_userdata('searchData'))
                    {
                        $post = $this->session->userdata('searchData');
                    }

                }

                $postCount = count($post);

                if($this->session->has_userdata('searchData') && $flag == 1)
                {
                    $ajaxData = $this->input->post();
                    $post = $this->session->userdata('searchData');
                    if(isset($ajaxData['continent_id']))
                        $post['continent_id'] = $ajaxData['continent_id'];
                    else
                        unset($post['continent_id']);
                    if(isset($ajaxData['country_id']))
                        $post['country_id'] = $ajaxData['country_id'];
                    else
                        unset($post['country_id']);
                    if(isset($ajaxData['city']) && !empty($ajaxData['city']))
                    {
                        $post['city'] = $ajaxData['city'];
                    }
                    else
                    {
                        unset($post['city']);
                    }
                    if(isset($ajaxData['wellness_style']))
                        $post['wellness_style'] = $ajaxData['wellness_style'];
                    else
                        unset($post['wellness_style']);

                    if(isset($ajaxData['parent_cat']))
                        $post['parent_cat'] = $ajaxData['parent_cat'];
                    else
                        unset($post['parent_cat']);

                    if(isset($ajaxData['categories']))
                        $post['categories'] = $ajaxData['categories'];
                    else
                        unset($post['categories']);

                    if(isset($ajaxData['rating']))
                        $post['rating'] = $ajaxData['rating'];
                    else
                        unset($post['rating']);

                    if(isset($ajaxData['price_range'][0]))
                        $post['price_range'] = $ajaxData['price_range'][0];
                    else
                        unset($post['price_range']);

                    if($post['price_range'] == '0,0' || $post['price_range'] == '0,1')
                    {
                        unset($ajaxData['price_range'][0]);
                        unset($post['price_range']);
                    }

                    //added by Rakesh to correct price filter
                    if(isset($ajaxData['price_filter_change']))
                        $post['price_filter_change'] = $ajaxData['price_filter_change'];
                    else
                        unset($post['price_filter_change']);

                    if(isset($ajaxData['min_range']))
                        $post['min_range'] = $ajaxData['min_range'];
                    else
                        unset($post['min_range']);

                    if(isset($ajaxData['max_range']))
                        $post['max_range'] = $ajaxData['max_range'];
                    else
                        unset($post['max_range']);

                    $post['search_text'] = '';
                }
                else
                {
                    if(isset($post['rating']))
                        unset($post['rating']);
                    if(isset($post['price_range']))
                        unset($post['price_range']);
                }

                $post['date1'] = '';
                $post['date2'] = '';
                if(!isset($post['datefilter']))
                {
                    $post['datefilter'] = get_date_range();
                }
                if(isset($post['datefilter']) && !empty($post['datefilter']))
                {
                    $date = explode(' - ', $post['datefilter']);

                    $post['date1'] = format_date($date['0']);
                    $post['date2'] = format_date($date['1']);
                }

                if($this->session->has_userdata('expediaData'))
                {
                    $this->session->unset_userdata('expediaData');
                }

                if(isset($city) && !empty($city))
                {
                    if(isset($city[0]))
                    {
                        $post['city'] = $city;
                    }
                    else
                    {
                        $post['city'][] = $city;
                    }
                }
                $cityList = array();
                $post['cityList'] = '';

                if(isset($post['city']) && (is_array($post['city']) && in_array('Bali', $post['city'])
                        || (isset($city) && $city == '193774')
                        || (is_array($post['city']) && in_array('193774', $post['city'])) ))
                {
                    $param = 'Bali';
                    $cityList+= $this->get_cities_base_on_state_nacl($param);
                    $post['cityList'] = $cityList;
                    if(isset($city) && !empty($city) && count($city) == 1 && $city == '193774')
                    {
                        unset($post['city']);
                        $post['city'][0] = 'Bali';
                    }
                }
                if(isset($post['city']) && (
                        is_array($post['city'])  && in_array('Hawaii', $post['city']) || (isset($city) && $city == '193775') || (in_array('193775', $post['city']))))
                {
                    $param = 'Hawaii';
                    $cityList+= $this->get_cities_base_on_state_nacl($param);
                    $post['cityList'] = $cityList;
                    if(isset($city) && !empty($city) && count($city) == 1 && $city == '193775')
                    {
                        unset($post['city']);
                        $post['city'][0] = "Hawaii";
                    }
                    else if(isset($city) && !empty($city) && count($post['city']) > 1)
                    {
                        $post['city'][2] = "Hawaii";
                    }
                }



                if(isset($post['country_id']) && !empty($post['country_id']))
                {


                    if(isset($post['country_id']) && in_array('C1', $post['country_id']))
                    {
                        $post['country_id']['991'] = 'AG';
                        $post['country_id']['992'] = 'GD';
                        $post['country_id']['993'] = 'DO';
                        $post['country_id']['994'] = 'LC';
                        $post['country_id']['995'] = 'TC';
                        $post['country_id']['996'] = 'DM';
                    }
                }

                if(isset($get_data[0]) && $get_data[0]=='z'  && count($get_data)==2)
                {
                    $flag = 2 ;
                }

                if(isset($post['sort_by']) && !empty($post['sort_by']) && $flag == 0 && (count($post) == 1 || $postCount == 1))
                {

                    $sort_by = $post['sort_by'];
                    if($this->session->has_userdata('searchData'))
                    {
                        $post = $this->session->userdata('searchData');
                    }
                    $post['sort_by'] = $sort_by;
                }
                if($this->session->has_userdata('searchData') && count($post) > 1 && $flag == 0)
                {
                    $this->session->unset_userdata('searchData');
                }


                if(isset($post['search_type']) && !empty($post['search_type']) && $post['search_type'] == 'country')
                {
                    $post['country_id'][] = $post['search_index'];
                }

                if(isset($post['categories']))
                {
                    $this->db->where_in('c.id', $post['categories']);
                    $categories_activities = $this->app_model->find('wellness_treatment_activities c', 'list', array('c.id', 'c.wellness_treatment_activities_categories_id'), array('c.is_active' => 1));

                    if(isset($categories_activities) && !empty($categories_activities))
                        $this->data['categories_activities'] = $categories_activities;
                    else
                        $this->data['categories_activities'] = array();
                }
                $city_where = '';
                $this->data['con_country_data'] = array();
                //pr($post) ;
                $flag = 0 ; // dont remove
                if(isset($post['country_id']) && !empty($post['country_id']))
                {
                    $this->db->where_in('c.country_name', $post['country_id']);
                    $country_data = $this->app_model->find('manage_search_destinations d', 'list', array('cc.name', 'cc.name'), array('d.is_active' => 1), array(
                        array('manage_continents cc', 'cc.id=d.continent_id AND cc.is_active=1 AND cc.is_deleted=0', 'left'),
                        array('countries c', 'c.iso2=d.country_id AND c.is_active=1 AND c.is_deleted=0', 'left')
                    ));



                    if(isset($country_data) && !empty($country_data))
                        $this->data['con_country_data'] = array_keys($country_data);
                    else
                        $this->data['con_country_data'] = array();


                    $city_list = array();

                    //pr($post) ;
                    foreach($post['country_id'] as $country_id)
                    {
                        if($city_where == '')
                        {
                            //$get_city = get_cities_list_base_on_country_id($country_id);
                            if(isset($get_city) && !empty($get_city))
                            {
                                $city_list = $get_city;
                            }

                            $city_where.=" hs.search_destination = '{$country_id}' ";
                        }
                        else
                        {
                            //$get_city = get_cities_list_base_on_country_id($country_id);
                            if(isset($get_city) && !empty($get_city))
                            {
                                $city_list+= $get_city;
                            }
                            $city_where.="  OR hs.search_destination = '{$country_id}' ";
                        }
                    }
                }

                //pr($post) ;

                if(!isset($post['city']) && !empty($city_list))
                {
                    $post['city'] = array();
                    $post['city']+=$city_list;
                }



                if((isset($post['city']) && !empty($post['city'])))
                {
                    //$post['city']+=$city_list ;

                    $this->db->where_in('city.city_id', $post['city']);
                    $this->db->or_where_in('city.city_name', $post['city']);
                    if(isset($post['cityList']) && !empty($post['cityList']))
                    {
                        $this->db->or_where_in('city.city_name', $post['cityList']);
                    }
                    $country_data = $this->app_model->find('manage_search_destinations d', 'list', array('cc.name', 'd.country_id'), array('d.is_active' => 1), array(
                        array('manage_continents cc', 'cc.id=d.continent_id AND cc.is_active=1 AND cc.is_deleted=0', 'left'),
                        array('cities city', 'd.city_id=city.city_id AND city.is_active=1 AND city.is_deleted=0', 'left')
                    ));

                    if(isset($country_data) && !empty($country_data))
                    {
                        $con_country_data = array_keys($country_data);
                        if(isset($con_country_data) && !empty($con_country_data))
                        {
                            $start = 999;
                            foreach($con_country_data as $contentCountry)
                            {
                                $this->data['con_country_data'][$start] = $contentCountry;
                                $start++;
                            }
                        }

                        $this->data['city_country_data'] = array_values($country_data);
                    }
                    else
                    {
                        //$this->data['con_country_data'] = array() ;
                    }
                }

                $post['con_country_data'] = $this->data['con_country_data'];

                if(isset($post['city'][0]) && !empty($post['city']))
                {

                    //$this->db->or_where_in('hs.City', $post['city']);
                    foreach($post['city'] as $city_id)
                    {
                        if($city_where == '')
                        {
                            $city_where.=" hs.search_destination = '{$city_id}' ";
                        }
                        else
                        {
                            $city_where.=" OR hs.search_destination = '{$city_id}' ";
                        }
                    }
                    /* if(isset($post['cityList']) && !empty($post['cityList']))
                      {
                      $city_where_new = '';
                      foreach($post['cityList'] as $city_id)
                      {
                      if($city_where=='')
                      {
                      $city_where.=" hs.City = '{$city_id}' " ;

                      }
                      else
                      {
                      $city_where.=" OR hs.City = '{$city_id}' " ;
                      }
                      }
                      }
                     */
                    //echo $city_where ; die('123') ;
                }
                if($city_where != '')
                {
                    $city_where_condition = '(' . $city_where . ' )';
                    //echo $city_where_condition ; die ;
                    $this->db->where($city_where_condition);
                }
                $style_where = '';
                if(isset($post['wellness_style']) && !empty($post['wellness_style']))
                {

                    foreach($post['wellness_style'] as $style)
                    {
                        if($style_where == '')
                            $style_where = "FIND_IN_SET({$style},shs.styles_ids) !=0";
                        else
                            $style_where.= " OR  FIND_IN_SET({$style},shs.styles_ids) !=0";
                    }
                }
                if(isset($post['categories']) && !empty($post['categories']))
                {
                    //$style_where = '';
                    foreach($post['categories'] as $style)
                    {
                        if($style_where == '')
                            $style_where = "FIND_IN_SET({$style},shs.activities_ids) !=0";
                        else
                            $style_where.= " OR  FIND_IN_SET({$style},shs.activities_ids) !=0";
                    }
                    //$style_where = '(' . $style_where . ')';
                    //$this->db->where($style_where);
                }

                if(isset($style_where) && !empty($style_where))
                {
                    //echo $style_where ; die ;
                    $style_where = '(' . $style_where . ')';
                    $this->db->where($style_where);
                }
                if(isset($post['search_text']) && !empty($post['search_text']))
                {
                    $post['search_text'] = trim($post['search_text']) ;
                }
                if((isset($post['search_text']) && !empty($post['search_text'])) && (isset($post['search_index']) && empty($post['search_index'])) && $flag == 0)
                {
                    $search_text = addslashes(addslashes($post['search_text']));
                    $or_where = '';
                    //$or_where.="(hs.Name LIKE '%{$search_text}%' OR hs.Name LIKE '%{$search_text}'  OR  c.country_name LIKE '%{$search_text}%' OR c.country_name LIKE '%{$search_text}' OR c.continent LIKE '%{$search_text}%'  OR c.continent LIKE '%{$search_text}') OR city.city_name LIKE '%{$search_text}') OR city.city_name LIKE '%{$search_text}%')";
                    $or_where.="(hs.Name LIKE '%{$search_text}%' OR hs.Name LIKE '%{$search_text}'  OR  hs.search_destination LIKE '%{$search_text}%' OR hs.search_destination LIKE '%{$search_text}' OR hs.search_destination LIKE '{$search_text}%' OR hs.search_destination = '{$search_text}')";
                    $this->db->where($or_where);
                }
                else if((isset($post['search_text']) && !empty($post['search_text'])) && (isset($post['search_index']) && !empty($post['search_index'])) && $flag == 0)
                {
                    if(isset($post['search_type']) && $post['search_type'] == 'hotel')
                    {
                        $this->db->where('hs.EANHotelID', $post['search_index']);
                    }
                    else if($post['search_type'] == 'country')
                    {
                        $this->db->where('hs.search_destination', $post['search_index']);
                    }
                }


                if(isset($post['country_id']) && empty($post['country_id']))
                {
                    unset($post['country_id']);
                }
                if(isset($post['city']) && empty($post['city']))
                {
                    unset($post['city']);
                }

                //pr($post) ;
                //pr($this->data['searchDestinationCities']) ;


                if(isset($post['search_text']) && !empty($post['search_text']) && isset($post['search_index']) && !empty($post['search_index']) && $flag == 0)
                {
                    $query = $this->app_model->find('hotels_select hs', 'all', array('hs.*'), array('hs.is_active' => 1), array(
                            array('select_hotel_styles shs', 'shs.EANHotelID=hs.EANHotelID', 'LEFT'),
                            array('countries c', 'hs.Country=c.iso2', 'LEFT')
                            //array('cities city', 'c.country_id=city.country_id', 'LEFT'),
                        )
                    );
                }
                else if(isset($post['search_text']) && !empty($post['search_text']) && $flag == 0)
                {
                    $query = $this->app_model->find('hotels_select hs', 'all', array('hs.*'), array('hs.is_active' => 1), array(
                            array('select_hotel_styles shs', 'shs.EANHotelID=hs.EANHotelID', 'LEFT'),
                            array('countries c', 'hs.Country=c.iso2', 'LEFT')
                            //array('cities city', 'c.country_id=city.country_id', 'LEFT'),
                        )
                    );
                }
                else if(( (isset($post['country_id']) && empty($post['country_id'])) || (isset($post['city']) && empty($post['city']))) && (empty($post['wellness_style']) && empty($post['categories'])))
                {
                    $query = array();
                }
                else if(( (!isset($post['country_id']) ) && (!isset($post['city']) )) && (!isset($post['wellness_style']) && !isset($post['categories'])))
                {

                    $query = array();
                }
                else
                {
                    $query = $this->app_model->find('hotels_select hs', 'all', array('hs.*'), array('hs.is_active' => 1, 'hs.search_destination !=' => ''), array(array('select_hotel_styles shs', 'shs.EANHotelID=hs.EANHotelID', 'LEFT')));
                }
                //echo $this->db->last_query(); die ;
                if(!isset($post['rooms']))
                {
                    $post['rooms'] = $this->data['search']['rooms'];
                }
                if(isset($query) && !empty($query))
                {
                    foreach($query as $content)
                    {
                        $hotelsData[$content['EANHotelID']] = $content;
                        $this->db->order_by('images.is_main', 'DESC');
                        $images = $this->app_model->find('hotel_images images', 'all', 'images.*', array('images.EANHotelID' => $content['EANHotelID']));
                        $hotelsData[$content['EANHotelID']]['images'] = $images;
                    }
                    $hotelsId = array_keys($hotelsData);

                    $hotelsId = implode(',', $hotelsId);
                    $room = isset($post['rooms']) ? $post['rooms'] : '';
                    $call_api = $this->call_ean_api('list', selected_currency, $hotelsId, 'en_US', $post['date1'], $post['date2'], $room, $post);

                    $decode = json_decode($call_api, true);
                    // pr($decode) ;
                    $this->data['hotellist'][] = $decode;
                    $content = $this->recursive_fetch_ean($decode);
                    /* pr($decode['HotelListResponse']['cacheKey']) ;
                      pr($decode['HotelListResponse']['cacheLocation']) ; */

                    if(isset($content) && !empty($content))
                    {
                        foreach($content as $decode)
                        {
                            //pr($decode['HotelListResponse']['HotelList']) ;
                            if(isset($decode['HotelListResponse']['HotelList']['HotelSummary']) && !empty($decode['HotelListResponse']['HotelList']['HotelSummary']))
                            {
                                $check_hotel_length = $decode['HotelListResponse']['HotelList']['@size'];
                                if($check_hotel_length <= 1)
                                {
                                    $hotel = $decode['HotelListResponse']['HotelList']['HotelSummary'];

                                    if(isset($hotelsData[$hotel['hotelId']]) && !empty($hotelsData[$hotel['hotelId']]))
                                    {
                                        $expedia[$hotel['hotelId']] = $hotelsData[$hotel['hotelId']];
                                        $expedia[$hotel['hotelId']]['expedia'] = $hotel;
                                        $expedia[$hotel['hotelId']]['is_wish'] = $this->is_check_hotels_and_deals_wishlist($hotel['hotelId'], 'hotels');
                                        $expedia[$hotel['hotelId']]['activities'] = $this->get_hotels_style($hotel['hotelId']);
                                        if(isset($post['price_range']) && !empty($post['price_range']) && (isset($post['price_filter_change']) && $post['price_filter_change'] == 1 ))
                                        {
                                            $amt = isset($hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate']) ? $hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate'] : 0;
                                            $price_split = explode(',', $post['price_range']);
                                            if($price_split[0] <= $amt && $price_split[1] >= $amt)
                                            {

                                                $sortByPrice[$hotel['hotelId']] = $amt;
                                            }
                                        }
                                        else
                                        {
                                            $sortByPrice[$hotel['hotelId']] = isset($hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate']) ? $hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate'] : 0;
                                        }
                                    }
                                }
                                else
                                {
                                    //pr($decode['HotelListResponse']['HotelList']) ;
                                    foreach($decode['HotelListResponse']['HotelList']['HotelSummary'] as $hotel)
                                    {
                                        if(isset($hotelsData[$hotel['hotelId']]) && !empty($hotelsData[$hotel['hotelId']]))
                                        {
                                            $expedia[$hotel['hotelId']] = $hotelsData[$hotel['hotelId']];
                                            $expedia[$hotel['hotelId']]['expedia'] = $hotel;
                                            $expedia[$hotel['hotelId']]['is_wish'] = $this->is_check_hotels_and_deals_wishlist($hotel['hotelId'], 'hotels');
                                            $expedia[$hotel['hotelId']]['activities'] = $this->get_hotels_style($hotel['hotelId']);
                                            //$sortByPrice[$hotel['hotelId']] = isset($hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate']) ? $hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate'] : 0;
                                            if(isset($post['price_range']) && !empty($post['price_range']) && (isset($post['price_filter_change']) && $post['price_filter_change'] == 1 ))
                                            {
                                                $amt = isset($hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate']) ? $hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate'] : 0;
                                                $price_split = explode(',', $post['price_range']);
                                                if($price_split[0] <= $amt && $price_split[1] >= $amt)
                                                {

                                                    $sortByPrice[$hotel['hotelId']] = $amt;
                                                }
                                            }
                                            else
                                            {
                                                $sortByPrice[$hotel['hotelId']] = isset($hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate']) ? $hotel['RoomRateDetailsList']['RoomRateDetails']['RateInfos']['RateInfo']['ChargeableRateInfo']['@averageRate'] : 0;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $this->data['startPrice'] = 0;
            $this->data['endPrice'] = 0;
            //pr($post);
            if(isset($sortByPrice) && !empty($sortByPrice))
            {
                asort($sortByPrice);
                if(isset($post['sort_by']) && !empty($post['sort_by']) && $post['sort_by'] == 2)
                {
                    arsort($sortByPrice);
                }
            }
            if(isset($expedia) && !empty($expedia))
            {
                $total = count($sortByPrice);
                $this->data['totalPages'] = ceil($total / self::$per_page);

                $this->data['startPrice'] = current(($sortByPrice));
                $this->data['endPrice'] = end($sortByPrice);
            }
            $this->session->set_userdata('expediaData', $expedia);
            //$this->session->set_userdata('searchData',array()) ;


            $this->session->set_userdata('sortByPrice', $sortByPrice);
            $this->data['expedia'] = $expedia;
            if(!empty($expedia) && count($expedia) > self::$per_page)
            {
                $this->data['expediaData'] = array_slice($sortByPrice, 0, self::$per_page, true);
            }
            else
            {
                $this->data['expediaData'] = $sortByPrice;
            }
            if($this->session->has_userdata('room_filter'))
            {
                $room_filter = $this->session->userdata('room_filter');
                $this->data['room_filter'] = $room_filter;
            }
            if(!isset($post['sort_by']))
            {
                $post['sort_by'] = 1;
            }
            if(isset($post['search_text']) && !empty($post['search_text']))
            {
                unset($post['continent_id']);
            }

            if(isset($post['country_id']) && in_array('B1', $post['country_id']))
            {
                unset($post['country_id']['999']);
                unset($post['country_id']['998']);
            }

            if(isset($post['country_id']) && in_array('C1', $post['country_id']))
            {
                unset($post['country_id']['991']);
                unset($post['country_id']['992']);
                unset($post['country_id']['993']);
                unset($post['country_id']['994']);
                unset($post['country_id']['995']);
                unset($post['country_id']['996']);
            }
            $this->data['post'] = $post;

            $this->session->set_userdata('searchData', $post);
            //unset($post) ;
            //unset($_POST) ;
            //pr($this->data['post']) ;
            //pr($this->data['post']) ;
            //added by Rakesh to correct price filter start
            if(isset($post['price_range']) && (isset($post['price_filter_change']) && $post['price_filter_change'] == 1 ))
            {
                if($flag == 0 && isset($post['sort_by']) && $post['sort_by'] == 2)
                {

                }
                else
                {
                    $price_split = explode(',', $post['price_range']);
                    $this->data['startPrice'] = ($price_split[0] < $this->data['startPrice']) ? $price_split[0] : $this->data['startPrice'] - 1;
                    $this->data['endPrice'] = ($price_split[1] > $this->data['endPrice']) ? $price_split[1] : $this->data['endPrice'] + 1;
                }
            }
            //added by Rakesh to correct price filter end

            if($this->data['endPrice'] > $this->data['startPrice'])
            {
                $this->data['startPrice'] = floor($this->data['startPrice']) - 1;
                $this->data['endPrice'] = ceil($this->data['endPrice']) + 1;
            }
            if($this->data['startPrice'] > $this->data['endPrice'])
            {
                $this->data['startPrice'] = ceil($this->data['startPrice']) + 1;
                $this->data['endPrice'] = floor($this->data['endPrice']) - 1;
            }

            //added by Rakesh to correct price filter start
            if(isset($post['max_range']) && (isset($post['price_filter_change']) && $post['price_filter_change'] == 1 ))
            {
                $this->data['price_range_start'] = $post['min_range'];
                $this->data['price_range_end'] = $post['max_range'];
            }
            else
            {
                if($this->data['startPrice'] > $this->data['endPrice'])
                {
                    $this->data['price_range_start'] = $this->data['endPrice'];
                    $this->data['price_range_end'] = $this->data['startPrice'];
                }
                else
                {
                    $this->data['price_range_start'] = $this->data['startPrice'];
                    $this->data['price_range_end'] = $this->data['endPrice'];
                }
            }
            //added by Rakesh to correct price filter end
//pr($this->data);

            if($this->input->is_ajax_request())
            {
                $flag = 1;
            }
            if($flag == 0)
            {

                if(isset($post['search_type']) && !empty($post['search_type']) && $post['search_type'] == 'hotel' && !empty($post['search_index']) && count($expedia) == 1)
                {
                    redirect(base_url() . 'hotel_detail/' . $post['search_index']);
                }
                else
                {

                    $meta_title = COMPANY_NAME . " - " . COMPANY_TAGLINE;
                    $meta_description = COMPANY_NAME . " - " . COMPANY_TAGLINE;
                    if(isset($get_data) && !empty($get_data))
                    {
                        $post['get_data'] = $get_data ;
                        //pr($post['get_data']) ;
                    }



                    if(isset($post['continent_id']) && !empty($post['continent_id']))
                    {
                        $post['continent_id'] = array_flip($post['continent_id']) ;
                    }

                    if((isset($post['get_data'][0]) && isset($post['get_data'][2]) && isset($post['get_data'][4]) ) && $post['get_data'][0]=='ct' && $post['get_data'][2]=='s' && $post['get_data'][4]=='at')
                    {
                        $con_country_data = array_flip($post['con_country_data']);
                        $ct_name = key($con_country_data) ;
                        //$chk_meta = get_landing_wellness_data_all($post['get_data'][1],$post['get_data'][3],$ct_name);

                        $treatment_Cat = getCategoryTreatmentCatName($post['get_data']['5']);
                        $treatment_list = get_activities_per_Category_data($post['get_data']['5']);
                        $treatment_list_limit = array_slice($treatment_list,0,3) ;
                        $activities = implode(', ',$treatment_list_limit);
                        $wellness =  $this->get_wellness_data ();
                        $wellness_name = $wellness['wellness_style'][$post['get_data']['3']] ;

                        $meta_title = "Book Online Resorts {$wellness_name} & {$treatment_Cat[$post['get_data']['5']]} In  {$ct_name} | In This Life Wellness Travel." ;
                        $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find, Book & then {$activities} at a {$wellness_name} in {$ct_name}." ;
                    }

                    else if((isset($post['get_data'][0]) && isset($post['get_data'][2]) && isset($post['get_data'][4]) ) && $post['get_data'][0]=='c' && $post['get_data'][2]=='s' && $post['get_data'][4]=='a')
                    {

                        $ct_name = $post['country_id'][0] ;
                        $chk_meta = get_landing_wellness_data_all($post['get_data'][3],$post['get_data'][5],$ct_name);
                        if(!empty($chk_meta))
                        {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        }
                        else
                        {

                            $activity_name = get_activities_data($post['get_data'][5]) ;
                            $treatment_Cat = getCategoryTreatmentCat($post['get_data'][5]);
                            $treatment_list = get_activities_per_Category_data($treatment_Cat[0]);
                            unset($treatment_list[$post['categories'][0]]) ;
                            $activities = $activity_name['name'] ;
                            $arg = end($treatment_list) ;
                            // $activities.=', '.$arg ;
                            $wellness =  $this->get_wellness_data ();
                            $wellness_name = $wellness['wellness_style'][$post['get_data']['3']] ;

                            $meta_title = "Book Online Resorts {$wellness_name} & {$activities} In  {$ct_name} | In This Life Wellness Travel." ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find, Book & then {$activities} at a {$wellness_name} in {$ct_name}." ;
                        }
                    }



                    else if((isset($post['get_data'][0]) && isset($post['get_data'][2])) && $post['get_data'][0]=='c' && $post['get_data'][2]=='a')
                    {
                        $ct_name = $post['country_id'][0] ;
                        $chk_meta = get_landing_activities_new_data($post['get_data'][3],$ct_name);
                        if(!empty($chk_meta))
                        {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        }
                        else
                        {

                            $activity_name = get_activities_data($post['get_data'][3]) ;
                            $treatment_Cat = getCategoryTreatmentCat($post['get_data'][3]);
                            $treatment_list = get_activities_per_Category_data($treatment_Cat[0]);
                            unset($treatment_list[$post['categories'][0]]) ;
                            $activities = $activity_name['name'] ;
                            $arg = end($treatment_list) ;
                            $activities.=', '.$arg ;
                            $meta_title = "Book Online {$activities} Hotels & Resorts, Spas & Retreats In  {$ct_name}| In This Life Wellness Travel" ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness & {$activities} & more in {$ct_name}." ;
                        }
                    }
                    else if((isset($post['get_data'][0]) && isset($post['get_data'][2])) && $post['get_data'][0]=='z' && $post['get_data'][2]=='a')
                    {

                        $ct_name = $post['city'][0] ;
                        $chk_meta = get_landing_activities_new_data($post['get_data'][3],$ct_name);
                        if(!empty($chk_meta))
                        {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        }
                        else
                        {

                            $activity_name = get_activities_data($post['get_data'][3]) ;
                            $treatment_Cat = getCategoryTreatmentCat($post['get_data'][3]);
                            $treatment_list = get_activities_per_Category_data($treatment_Cat[0]);
                            unset($treatment_list[$post['categories'][0]]) ;
                            $activities = $activity_name['name'] ;
                            $arg = end($treatment_list) ;
                            $activities.=', '.$arg ;

                            $meta_title = "Book Online {$activities} Hotels & Resorts, Spas & Retreats In  {$ct_name}| In This Life Wellness Travel" ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness & {$activities} & more in {$ct_name}." ;
                        }
                    }
                    else if((isset($post['get_data'][0]) && isset($post['get_data'][2])) && $post['get_data'][0]=='ct' && $post['get_data'][2]=='a')
                    {
                        $con_country_data = array_flip($post['con_country_data']);
                        $ct_name = key($con_country_data) ;
                        $chk_meta = get_landing_activities_new_data($post['get_data'][3],$ct_name);
                        if(!empty($chk_meta))
                        {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        }
                        else
                        {

                            $activity_name = get_activities_data($post['get_data'][3]) ;
                            $treatment_Cat = getCategoryTreatmentCat($post['get_data'][3]);
                            $treatment_list = get_activities_per_Category_data($treatment_Cat[0]);
                            unset($treatment_list[$post['categories'][0]]) ;
                            $activities = $activity_name['name'] ;
                            $arg = end($treatment_list) ;
                            $activities.=', '.$arg ;

                            $meta_title = "Book Online {$activities} Hotels & Resorts, Spas & Retreats In  {$ct_name}| In This Life Wellness Travel" ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness & {$activities} & more in {$ct_name}." ;
                        }
                    }
                    else if((isset($post['get_data'][0]) && isset($post['get_data'][2])) && $post['get_data'][0]=='c' && $post['get_data'][2]=='s')
                    {
                        $ct_name = $post['country_id'][0] ;
                        $chk_meta = get_landing_style_data($post['get_data'][3],$ct_name);
                        if(!empty($chk_meta))
                        {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        }
                        else
                        {
                            $wellness =  $this->get_wellness_data ();
                            $wellness_name = $wellness['wellness_style'][$post['get_data']['3']] ;
                            $meta_title = "Book Online {$wellness_name} Hotels & Resorts, Spas & Retreats In  {$ct_name} | In This Life Wellness Travel" ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness & {$wellness_name} Hotel, Spa, Resort, Retreat, Yoga retreat or Meditation Retreat & more in {$ct_name}." ;
                        }
                    }
                    else if((isset($post['get_data'][0]) && isset($post['get_data'][2])) && $post['get_data'][0]=='ct' && $post['get_data'][2]=='s')
                    {
                        $con_country_data = array_flip($post['con_country_data']);
                        $ct_name = key($con_country_data) ;
                        $chk_meta = get_landing_style_data($post['get_data'][3],$ct_name);
                        if(!empty($chk_meta))
                        {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        }
                        else
                        {
                            $wellness =  $this->get_wellness_data ();
                            $wellness_name = $wellness['wellness_style'][$post['get_data']['3']] ;
                            $meta_title = "Book Online {$wellness_name} Hotels & Resorts, Spas & Retreats In  {$ct_name} | In This Life Wellness Travel" ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your {$wellness_name} & ECO Hotel, Spa, Resort, Retreat, Yoga retreat or Meditation Retreat & more in {$ct_name}." ;
                        }
                    }

                    else if((isset($post['get_data'][0]) && isset($post['get_data'][2])) && $post['get_data'][0]=='z' && $post['get_data'][2]=='s')
                    {
                        $ct_name = $post['city'][0] ;
                        $chk_meta = get_landing_style_data($post['get_data'][3],$ct_name);
                        if(!empty($chk_meta))
                        {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        }
                        else
                        {
                            $wellness =  $this->get_wellness_data ();
                            $wellness_name = $wellness['wellness_style'][$post['get_data']['3']] ;
                            $meta_title = "Book Online {$wellness_name} Hotels & Resorts, Spas & Retreats In  {$ct_name} | In This Life Wellness Travel" ;
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your {$wellness_name} & ECO Hotel, Spa, Resort, Retreat, Yoga retreat or Meditation Retreat & more in {$ct_name}." ;
                        }
                    }

                    else if((isset($post['get_data'][0]) && isset($post['get_data'][2]) ) && $post['get_data'][0]=='ct' && $post['get_data'][2]=='at')
                    {

                        $con_country_data = array_flip($post['con_country_data']);
                        $ct_name = key($con_country_data) ;

                        $treatment_Cat = getCategoryTreatmentCatName($post['get_data']['3']);
                        $treatment_list = get_activities_per_Category_data($post['get_data']['3']);
                        $treatment_list_limit = array_slice($treatment_list,0,3) ;
                        $activities = implode(', ',$treatment_list_limit);
                        $meta_title = "Book Online Wellness Hotels & Retreats in {$ct_name} | {$treatment_Cat[$post['get_data']['3']]} | In This Life Wellness Travel" ;
                        $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find, Book & then {$activities} at a Wellness Hotel, Health Spa, Luxury Wellness Spa or Eco Resort/Retreat in {$ct_name}." ;
                    }
                    else if(isset($post['continent_id']) && count($post['continent_id']) == 1 && isset($post['wellness_style']) && count($post['wellness_style']) == 1 && isset($post['get_data']))
                    {
                        $con_country_data = array_flip($post['con_country_data']);
                        if(count($con_country_data) == 1)
                        {
                            $continentList = get_continents();
                            $continent_name = strtoupper($continentList[$post['continent_id'][0]]);
                            $meta_title = "Book Online Wellness Hotels, Spas, Resorts & Retreats {$continent_name} | In This Life Wellness Travel";
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness Hotel, Spa, Resort & Retreat, Yoga retreat, Meditation Retreat & more in {$continent_name}.";
                        }
                    }
                    else if(isset($post['continent_id']) && count($post['continent_id']) == 1 && isset($post['get_data']))
                    {
                        $continentList = get_continents();
                        $con_country_data = array_flip($post['con_country_data']);
                        $destinationID = reset($post['continent_id']) ;
                        if(isset($destinationID) && !empty($destinationID))
                        {
                            $destinationName = $continentList[$destinationID] ;
                            $chk_meta  = get_landing_destination_data($destinationName);
                        }
                        else if($post['get_data'][0]=='ct')
                        {
                            $destinationName = $continentList[$post['get_data'][1]] ;
                            $chk_meta  = get_landing_destination_data($destinationName);
                        }

                        if(!empty($chk_meta))
                        {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        }
                        else
                        {
                            if(count($con_country_data) == 1)
                            {
                                // pr($continentList) ;
                                if(isset($continentList[$post['continent_id'][0]]))
                                {
                                    $continent_name = strtoupper($continentList[$post['continent_id'][0]]);
                                }
                                else
                                {
                                    $continent_name = strtoupper($continentList[$post['get_data'][1]]);
                                }
                                $meta_title = "Book Online Wellness Hotels, Spas, Resorts & Retreats {$continent_name} | In This Life Wellness Travel";
                                $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness Hotel, Spa, Resort & Retreat, Yoga retreat, Meditation Retreat & more in {$continent_name}.";
                            }
                        }

                        $continentsData = $this->get_continents_data();

                        $continentId = $post['get_data'][0];
                        // Nb: for some reason on teh second load of the search page, the get_data post value changes to being an array containing the saerch type (e.g. "ct") and the ID, so we look at the second element in the array if that is the case
                        if(count($post['get_data']) == 2) {
                            $continentId = $post['get_data'][1];
                        }

                        $continent = multidimensional_array_search_by_key($continentId, 'id', $continentsData);
                        $this->data['intro_content_title'] = $continent['name'];
                        $this->data['intro_content_text'] = $continent['short_description'];
                    }
                    else if(isset($post['country_id']) && count($post['country_id']) == 1 && isset($post['get_data']))
                    {

                        $country_name = strtoupper($post['country_id'][0]);
                        $chk_meta  = get_landing_destination_data($country_name);
                        if(!empty($chk_meta))
                        {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        }
                        else
                        {
                            $meta_title = "Book Online Wellness Hotels, Spas, Resorts & Retreats {$country_name} | In This Life Wellness Travel";
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness Hotel, Spa, Resort & Retreat, Yoga retreat, Meditation Retreat & more in {$country_name}.";
                        }

                        $destinationsData = $this->get_destination_data();

                        $countryId = $post['get_data'][0];
                        // Nb: for some reason on teh second load of the search page, the get_data post value changes to being an array containing the saerch type (e.g. "c") and the ID, so we look at the second element in the array if that is the case
                        if(count($post['get_data']) == 2) {
                            $countryId = $post['get_data'][1];
                        }

                        $country = multidimensional_array_search_by_key($countryId, 'country_id', $destinationsData);
                        $this->data['intro_content_title'] = $country_name;
                        $this->data['intro_content_text'] = $country['short_description'];

                    }
                    else if(isset($post['city']) && count($post['city']) == 1 && isset($post['get_data']))
                    {
                        $city_name = strtoupper($post['city'][0]);
                        $chk_meta  = get_landing_destination_data($city_name);
                        if(!empty($chk_meta))
                        {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        }
                        else
                        {
                            $meta_title = "Book Online Wellness Hotels, Spas, Resorts & Retreats {$city_name} | In This Life Wellness Travel";
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness Hotel, Spa, Resort & Retreat, Yoga retreat, Meditation Retreat & more in {$city_name}.";
                        }

                        $destinationsData = $this->get_destination_data();

                        $cityId = $post['get_data'][0];
                        // Nb: for some reason on teh second load of the search page, the get_data post value changes to being an array containing the saerch type (e.g. "z") and the ID, so we look at the second element in the array if that is the case
                        if(count($post['get_data']) == 2) {
                            $cityId = $post['get_data'][1];
                        }

                        $city = multidimensional_array_search_by_key($cityId, 'city_id', $destinationsData);
                        $this->data['intro_content_title'] = $city_name;
                        $this->data['intro_content_text'] = $city['short_description'];
                    }
                    else if(isset($post['wellness_style']) && count($post['wellness_style']) == 1 && isset($post['get_data']))
                    {
                        $wellnessData = $this->get_wellness_data();
                        $wellness_name = strtoupper($wellnessData['wellness_style'][$post['wellness_style'][0]]);
                        $chk_meta  = get_landing_wellness_data($post['wellness_style'][0]);
                        if(!empty($chk_meta))
                        {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        }
                        else
                        {
                            $meta_title = "Book Online {$wellness_name} Hotels & Resorts, Spas & Retreats | In This Life Wellness Travel";
                            $meta_description = "World’s only Wellness Travel Company with live availability. Search, Find & book your Wellness & {$wellness_name} Hotel, Spa, Resort, Retreat, Yoga retreat or Meditation Retreat & more.";
                        }

                        $this->data['intro_content_title'] = $wellnessData['wellness_style_detail'][$post['wellness_style'][0]]['name'];
                        $this->data['intro_content_text'] = $wellnessData['wellness_style_detail'][$post['wellness_style'][0]]['short_description'];
                    }

                    else if(isset($post['categories'][0]) && !empty($post['categories'][0]) && count($post['categories'])==1 && isset($post['get_data']))
                    {
                        $chk_meta =  get_landing_activities_data($post['categories'][0]) ;
                        if(!empty($chk_meta))
                        {
                            $meta_title = $chk_meta['meta_title'] ;
                            $meta_description =  $chk_meta['meta_description'] ;
                        }
                        else
                        {
                            $activity_name = get_activities_data($post['categories'][0]) ;
                            $treatment_Cat = getCategoryTreatmentCat($post['categories'][0]);
                            $treatment_list = get_activities_per_Category_data($treatment_Cat[0]);
                            unset($treatment_list[$post['categories'][0]]) ;
                            $activities = $activity_name['name'] ;
                            $arg = end($treatment_list) ;
                            $activities.=', '.$arg ;
                            $meta_title = "Book Online {$activity_name['name']}, Resorts, Hotels & Spas | In This Life Wellness Travel" ;
                            $meta_description =  "World’s only Wellness Travel Company with live availability. Search, Find and book your Wellness Hotel, Spa & Resort, {$activities} and more.";
                        }

                        $activityDetails = getCategoryTreatmentCatDetails($post['categories'][0]);
                        $this->data['intro_content_title'] =  $activityDetails['name'];
                        $this->data['intro_content_text'] = $activityDetails['short_description'];

                    }
                    else if(isset($post['get_data'][0]) && !empty($post['get_data'][0]) && $post['get_data']['0']=='at')
                    {
                        $treatment_Cat = getCategoryTreatmentCatName($post['get_data']['1']);
                        $treatment_list = get_activities_per_Category_data($post['get_data']['1']);
                        $treatment_list_limit = array_slice($treatment_list,0,3) ;
                        $activities = implode(', ',$treatment_list_limit);
                        $meta_title = "Book Online {$treatment_Cat[$post['get_data']['1']]}, Resorts, Hotels & Spas | In This Life Wellness Travel" ;
                        $meta_description =  "World’s only Wellness Travel Company with live availability. Search, Find and book your Wellness Hotel, Spa & Resort, {$activities} and more.";

                        $activityDetails = getCategoryTreatmentCatDetails($post['get_data']['1']);
                        $this->data['intro_content_title'] =  $activityDetails['name'];
                        $this->data['intro_content_text'] = $activityDetails['short_description'];
                    }
                    $meta_keyword = COMPANY_NAME . " - " . COMPANY_TAGLINE ;

                    if(isset($chk_meta) && !empty($chk_meta))
                    {
                        $this->data['meta_bottom'] =  $chk_meta['description'] ;
                        $meta_keyword = $chk_meta['meta_keyword'] ;
                    }

                    $this->template->write('title', $meta_title);
                    $this->template->write('meta_description', $meta_description);
                    $this->template->write('meta_keywords', $meta_keyword);

                    $this->template->write_view('content', 'front/home/search', $this->data);
                    $this->template->render();
                }
            }
            else
            {
                $total = isset($this->data['totalPages']) ? $this->data['totalPages'] : 0;
                $html = $this->load->view('front/home/common/product', $this->data, true);

                $response = array('html' => $html, 'page' => 2, 'total' => $total, 'startPrice' => ceil($this->data['startPrice']), 'endPrice' => ceil($this->data['endPrice']), 'price_range_start' => ceil($this->data['price_range_start']), 'price_range_end' => ceil($this->data['price_range_end']));
                echo json_encode($response);
                die;
            }
        }
    }

    public function details($hotel_id = null)
    {

        if($this->session->has_userdata('postData'))
        {
            $this->session->unset_userdata('postData');
        }


        if(!empty($hotel_id))
        {
            $content = array();
            /* if ( $this->session->has_userdata ( 'expediaData' ) )
              {
              $expediaData = $this->session->userdata ( 'expediaData' );

              $sortByPrice = $this->session->userdata ( 'sortByPrice' );

              // pr($roomData) ;
              $expedia = isset ( $expediaData[$hotel_id] ) ? $expediaData[$hotel_id] : '';

              //pr($searchData) ;
              } */
            $roomData = $this->session->userdata('room_filter');
            $searchData = $this->session->userdata('searchData');


            if(empty($searchData))
            {
                $searchData['rooms'] =$this->data['search']['rooms'] ;
                $searchData['datefilter'] = get_date_range();
                $split = explode(' - ',$searchData['datefilter']) ;
                $searchData['date1'] = date('m/d/Y', strtotime($split[0]));
                $searchData['date2'] = date('m/d/Y', strtotime($split[1]));
            }
            $this->data['searchData'] = $searchData;

//            pr($searchData) ;

            $post = $this->input->post();
            $postChange = false;
            if(isset($post) && !empty($post))
            {

                if(!isset($post['rooms']))
                    $this->data['searchData']['rooms'] = $this->data['search']['rooms'];
                else
                    $this->data['searchData']['rooms'] = $post['rooms'];

                $postChange = true;
                if(isset($post['date1']) && !empty($post['date1']))
                {
                    $concat = $post['date1'] . ' - ' . $post['date2'];
                    $post['date1'] = date('m/d/Y', strtotime($post['date1']));
                    $post['date2'] = date('m/d/Y', strtotime($post['date2']));
                    $this->data['searchData']['datefilter'] = $concat;
                    $this->data['searchData']['date1'] = $post['date1'];
                    $this->data['searchData']['date2'] = $post['date2'];
                }
                $this->session->set_userdata('searchData', $this->data['searchData']);
                $searchData = $this->session->userdata('searchData');
            }

            // Source hotel data from the database
            if((!isset($expedia) && empty($expedia)) || (isset($expedia) && !empty($expedia)) || ($postChange == true))
            {
                $this->db->where('hs.EANHotelID', $hotel_id);
                $query = $this->app_model->find('hotels_select hs', 'all', array('hs.*'), array('hs.is_active' => 1));

                // If results have been received
                if(isset($query) && !empty($query))
                {
                    // Loop through those results
                    foreach($query as $content)
                    {
//                        pr($content);
                        // Get the data for the hotel
                        $hotelsData[$content['EANHotelID']] = $content;
                        $this->db->order_by('images.is_main', 'DESC');
                        // Get images for the hotel
                        $images = $this->app_model->find('hotel_images images', 'all', 'images.*', array('images.EANHotelID' => $content['EANHotelID']));
                        $hotelsData[$content['EANHotelID']]['images'] = $images;
                        break;
                    }
                    $hotelsId = array_keys($hotelsData);
                    $hotelsId = implode(',', $hotelsId);
                    $room = isset($searchData['rooms']) ? $searchData['rooms'] : '';
                    if(isset($searchData) && !empty($searchData))
                    {

                        $call_api = $this->call_ean_api('list', selected_currency, $hotelsId, 'en_US', $searchData['date1'], $searchData['date2'], $room, $searchData);
                        $result = json_decode($call_api, true);
                        if(isset($result['HotelListResponse']['EanWsError']) && !empty($result['HotelListResponse']['EanWsError']))
                        {
                            $call_api = $this->call_ean_api('list', selected_currency, $hotelsId, 'en_US');
                        }
                        //pr($call_api) ;
                        //$call_api = $this->call_ean_api ( 'list', selected_currency, $hotelsId, 'en_US');
                    }
                    else
                    {
                        $call_api = $this->call_ean_api('list', selected_currency, $hotelsId, 'en_US');
                    }

                    $decode = json_decode($call_api, true);
                    //pr($decode) ;
                    $content[] = $decode;
//                    pr($content);
                    $expedia = array();
                    $sortByPrice = array();
//                    Removed by Josh Curtis 22 July 2020 because it's based on populating from EAN
//                    Replaced with lines immediately below populating the variables from our own database
//                    if(isset($content) && !empty($content))
//                    {
//                        foreach($content as $decode)
//                        {
//                            if(isset($decode['HotelListResponse']['HotelList']['HotelSummary']) && !empty($decode['HotelListResponse']['HotelList']['HotelSummary']))
//                            {
//                                // pr($decode['HotelListResponse']['HotelList']['HotelSummary']);
//                                $hotel = $decode['HotelListResponse']['HotelList']['HotelSummary'];
//                                if(isset($hotelsData[$hotel['hotelId']]) && !empty($hotelsData[$hotel['hotelId']]))
//                                {
//                                    $expedia[$hotel['hotelId']] = $hotelsData[$hotel['hotelId']];
//                                    $expedia[$hotel['hotelId']]['expedia'] = $hotel;
//                                    //$sortByPrice[$hotel['hotelId']] = $hotel['lowRate'];
//                                }
//                            }
//                        }
//                    }
                    // Get name into breadcrumb variable
                    $expedia['Name'] = $content['Name'];
                    // Get Name into page title variable
                    $expedia['expedia']['name'] = $content['Name'];
                    // Assemble address
                    $expedia['Address'] = $content['Address1'];
                    if($content['Address2'] != "") {
                        $expedia['Address'] .= ", " . $content['Address2'];
                    }
                    if($content['City'] != "") {
                        $expedia['Address'] .= ", " . $content['City'];
                        $state = get_state_name_base_on_city_name($content['City'],$content['Country']);
                        if($state != "") {
                            $expedia['Address'] .= ", " . $state;
                        }
                    }
                    if($content['PostalCode'] != "") {
                        $expedia['Address'] .= ", " . $content['PostalCode'];
                    }
                    $countryData = get_countries_list();
                    if($countryData[$content['Country']] != "") {
                        $expedia['Address'] .= ", " . $countryData[$content['Country']];
                    }
                    if($content['Location'] != "") {
                        $expedia['Address'] .= " <br>" . $content['Location'];
                    }

                    // Populate the map location
                    if($content['Latitude'] != "" && $content['Longitude'] != "") {
                        $expedia['Latitude'] = $content['Latitude'];
                        $expedia['Longitude'] = $content['Longitude'];
                    }

                    // Populate images
                    $expedia['images'] = $images;
                    // Get star rating
                    $expedia['expedia']['hotelRating'] = $content['StarRating'];

                }
                else
                {
                    $this->template->write('title', '404');
                    $this->template->write_view('content', '404', $this->data);
                    $this->template->render();
                    return false ;
                }

                //$expedia = isset($expedia[$hotel_id]) ? $expedia[$hotel_id] : '';

                if(isset($searchData) && !empty($searchData))
                {
                    $roomAvai = $this->call_ean_api_rooms('avail', selected_currency, $hotel_id, $locale = 'en_US', $searchData['date1'], $searchData['date2'], $searchData['rooms']);
                    $roomData = json_decode($roomAvai, true);
                    //pr($roomData)
                    $this->data['roomData'] = $roomData;
                    $this->session->set_userdata('roomData', $roomData);
                }

                $activities = $this->get_hotels_style($hotel_id);
                //pr($activities) ;
                if($this->session->has_userdata('room_filter'))
                {
                    $room_filter = $this->session->userdata('room_filter');
                    $this->data['room_filter'] = $room_filter;
                }
//pr($expedia);
                $this->data['expedia'] = $expedia;
                // Grab the hotel details into data set
                $this->data['hotel_detail'] = $this->app_model->find('hotel_details hd', 'first', 'hd.*', array('hd.EANHotelID' => $hotel_id));
                //pr($this->data['expedia']) ;
                $this->data['hotel_id'] = $hotel_id;
                $this->data['activities'] = $activities;
                $this->data['detail'] = $call_api = $this->call_ean_info('info', selected_currency, $hotelsId, 'en_US', '2');
                //pr(json_decode($this->data['detail'],true)) ;

                // Check if there' a hotel name set
                if(isset($query[0]['Name']) && !empty($query['meta_title']))
                {
                    $this->template->write('title', $query['meta_title']);
                }
                else if(isset($query[0]['Name']) && !empty($query[0]['Name']))
                {
                    $this->template->write('title', $query[0]['Name']);
                }
                else
                {
                    $this->template->write('title', 'Hotel Detail');
                }
                if(isset($query[0]['Name']) && !empty($query[0]))
                {
                    $this->template->write('meta_description', !empty($query[0]['meta_description']) ? $query[0]['meta_description'] : $query[0]['Name'] );
                    $this->template->write('meta_keywords', !empty($query[0]['meta_keywords']) ? $query[0]['meta_keywords'] : $query[0]['Name'] );
                }
            }
            else
            {
                redirect('/');
            }
            $this->template->write_view('content', 'front/home/details', $this->data);
            $this->template->render();
        }
        else
        {
            redirect('/');
        }
    }

    public function get_expedia_ajax()
    {
//        $sortByPrice =  $this->session->userdata('sortByPrice');
//        $this->data['expediaData'] = array_slice($sortByPrice,11,10,true) ;
//            pr($this->data['expediaData']);
        if(!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }

        if($this->session->has_userdata('expediaData'))
        {
            $data = $this->input->post();
            $expedia = $this->session->userdata('expediaData');
            $sortByPrice = $this->session->userdata('sortByPrice');
            $page = (isset($data['p']) && $data['p'] > 1) ? $data['p'] : 1;
            $current_page = $page - 1;
            $records_per_page = self::$per_page; // records to show per page
            $start = $current_page * $records_per_page;
            $start_no = $start + 1;
            $this->data['expediaData'] = array_slice($sortByPrice, $start, $records_per_page, true);
            $this->data['expedia'] = $expedia;
            $html = $this->load->view('front/home/common/product', $this->data, true);
            $data = array('html' => $html, 'start' => $start, 'end' => $records_per_page, 'page' => $page, 'no_product' => '1');
            echo json_encode($data);
            die;
        }
    }

    public function newsletter_subscription_22052018()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $data = $this->input->post();
        $name = $data['name'];
        $email = $data['email'];
        $emailnewsletter = $this->app_model->find('newsletter ns', 'first', '', array('ns.email' => $email));
        if (isset($emailnewsletter['email']) && $emailnewsletter['is_active'] == 0) {
            $updatedata['is_active'] = 1;
            $updatedata['name'] = $name;
            $updatedata['id'] = $emailnewsletter['id'];
            $save = $this->app_model->save('newsletter', $updatedata);
        } else {
            $save = $this->app_model->save('newsletter', $data);
        }
        if ($save) {
            $emailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'EC5'));
            if (!empty($emailTemplate)) {
                $searchArray = array('[Name]');

                $replaceArray = array($name);
                $emailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $emailTemplate['email_template']);
                //pr($emailTemplate['email_template']);
                $this->send_email($email, $emailTemplate['email_subject'], $emailTemplate['email_template'], $emailTemplate['email_from']);
            }
            $emailTemplate1 = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'EC6'));
            if (!empty($emailTemplate1)) {
                $searchArray = array('[Name]', '[Contact details]');
                $replaceArray = array($name, $email);
                $emailTemplate1['email_template'] = str_replace($searchArray, $replaceArray, $emailTemplate1['email_template']);
                //pr($emailTemplate1['email_template']);die;
                //pr($emailTemplate1);die;
                $this->send_email(email, $emailTemplate1['email_subject'], $emailTemplate1['email_template'], $emailTemplate1['email_from']);
            }
            //$mail = mail_chimp($data);
            $success = newsletter_successfully;
            $this->session->set_flashdata('success', newsletter_successfully);
            $data['subscription_status'] = "subscribed";
            $data['success'] = $success;
        } else {
            $error = newsletter_error;
            $data['error'] = $error;
        }
        echo json_encode($data);
        die;
    }

    public function newsletter_subscription()
    {
        if(!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $data = $this->input->post();
        $name = $data['name'];
        $email = $data['email'];
        if(isset($data['i_accept']))
        {
            unset($data['i_accept']);
        }
        $emailnewsletter = $this->app_model->find('newsletter ns', 'first', '', array('ns.email' => $email));
        if(isset($emailnewsletter['email']) && $emailnewsletter['is_active'] == 0)
        {
            $updatedata['is_active'] = 1;
            $updatedata['name'] = $name;
            $updatedata['id'] = $emailnewsletter['id'];
            $save = $this->app_model->save('newsletter', $updatedata);
        }
        else
        {
            $save = $this->app_model->save('newsletter', $data);
        }
        if($save)
        {
            $emailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'EC5'));
            if(!empty($emailTemplate))
            {
                $searchArray = array('[Name]');

                $replaceArray = array($name);
                $emailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $emailTemplate['email_template']);
                //pr($emailTemplate['email_template']);
                $this->send_email($email, $emailTemplate['email_subject'], $emailTemplate['email_template'], $emailTemplate['email_from']);
            }
            $emailTemplate1 = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'EC6'));
            if(!empty($emailTemplate1))
            {
                $searchArray = array('[Name]', '[Contact details]');
                $replaceArray = array($name, $email);
                $emailTemplate1['email_template'] = str_replace($searchArray, $replaceArray, $emailTemplate1['email_template']);
                //pr($emailTemplate1['email_template']);die;
                //pr($emailTemplate1);die;
                $this->send_email(email, $emailTemplate1['email_subject'], $emailTemplate1['email_template'], $emailTemplate1['email_from']);
            }
            //$mail = mail_chimp($data);
            $success = newsletter_successfully;
            if(!isset($data['is_popup']))
            {
                $this->session->set_flashdata('success', newsletter_successfully);
            }
            $data['subscription_status'] = "subscribed";
            $data['success'] = $success;
        }
        else
        {
            $error = newsletter_error;
            $data['error'] = $error;
        }
        echo json_encode($data);
        die;
    }

    public function destinations()
    {
        //$this->db->order_by ( 'cont_name', 'ASC' );
        //$this->db->order_by ( 'country_name', 'ASC' );
        $query = $this->app_model->find('manage_search_destinations d', 'all', array('d.*', 'cc.name as cont_name', 'cc.image as cont_image', 'c.country_name as country_name', 'city.city_name as city_name'), array('d.is_active' => 1), array(
            array('manage_continents cc', 'cc.id=d.continent_id AND cc.is_active=1 AND cc.is_deleted=0',
                'inner'),
            array('countries c', 'c.iso2=d.country_id AND c.is_active=1 AND c.is_deleted=0', 'inner'),
            array('cities city', 'city.city_id=d.city_id AND city.is_active=1 AND city.is_deleted=0', 'left')
        ), 'sort ASC');
        // pr($this->db->last_query());
        // pr($query);
        $destination_array = array();
        if(isset($query) && !empty($query))
        {
            foreach($query as $data)
            {
                $destination_array[$data['continent_id']]['cont_img'] = $data['cont_image'];
                if(isset($data['city_id']) && $data['city_id'] != 0 && $data['city_id'] != "")
                {
                    $destination_array[$data['continent_id']]['cont_name'][$data['cont_name']]['z-' . $data['city_id']] = $data['city_name'];
                }
                else
                {
                    $destination_array[$data['continent_id']]['cont_name'][$data['cont_name']][$data['country_id']] = $data['country_name'];
                }
            }

            //return $destination_array ;
        }
        // pr($destination_array);
        $this->data['destinationdata_array'] = $destination_array;

        $this->db->order_by('td.sort', 'ASC');
        $topdastination_array = $this->app_model->find('top_destination td', 'all', array('td.*', 'td.id as top_destination_id', 'c.country_name as country_name', 'city.city_name as city_name'), array('td.is_active' => 1), array(array('countries c', 'c.country_id=td.country_id AND c.is_active=1 AND c.is_deleted=0',
            'inner'),
            array('cities city', 'city.city_id=td.city_id AND city.is_active=1 AND city.is_deleted=0',
                'left')
        ));
        //pr($this->db->last_query());
        //pr($topdastination_array);
        $this->data['topdastinationdata_array'] = $topdastination_array;

        $this->template->write('title', 'Destinations' . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_description', COMPANY_NAME . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_keywords', COMPANY_NAME . " - " . COMPANY_TAGLINE);
        $this->template->write_view('content', 'front/page/dastinations', $this->data);
        $this->template->render();
    }

    public function wellness()
    {
        $this->db->order_by('ws.sort', 'ASC');

        $wellness_array = $this->app_model->find('wellness_styles ws', 'all', array('ws.*', 'ws.id as wellness_id', 'ws.name as wellness_name'), array('ws.is_active' => 1, 'ws.is_home' => 1));

        // pr($wellness_array);

        $this->data['wellnessdata_array'] = $wellness_array;

        $this->db->order_by('wtac.sort', 'ASC');
        $this->db->order_by('wta.sort', 'ASC');
        $query = $this->app_model->find('wellness_treatment_activities wta', 'all', array('wta.*', 'wtac.id as category_id', 'wtac.icon as category_icon', 'wtac.name as category_name',
            'wta.id as activities_id', 'wta.name as activities_name'), array('wta.is_active' => 1), array(
            array('wellness_treatment_activities_categories wtac', 'wtac.id=wta.wellness_treatment_activities_categories_id AND wtac.is_active=1 AND wtac.is_deleted=0',
                'inner')
        ));
        $wellnessstyles_array = array();
        // pr($query);

        if(isset($query) && !empty($query))
        {
            foreach($query as $data)
            {
                //$wellnessstyles_array[$data['category_id']]['content'] = $data['short_description'];
                $wellnessstyles_array[$data['category_id']]['cat_icon'] = $data['category_icon'];
                $wellnessstyles_array[$data['category_id']]['cat_name'][$data['category_name']][$data['activities_id']]['activities_name'] = $data['activities_name'];
                $wellnessstyles_array[$data['category_id']]['cat_name'][$data['category_name']][$data['activities_id']]['activities_image'] = $data['image'];
                $wellnessstyles_array[$data['category_id']]['cat_name'][$data['category_name']][$data['activities_id']]['short_description'] = $data['short_description'];
            }
            //return $destination_array ;
        }
        //pr($wellnessstyles_array);
        $this->data['wellnessstylesdata_array'] = $wellnessstyles_array;

        $this->template->write('title', 'Wellness Styles' . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_description', 'Wellness Styles' . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_keywords', COMPANY_NAME . " - " . COMPANY_TAGLINE);

        $this->template->write_view('content', 'front/page/wellnessstyles', $this->data);
        $this->template->render();
    }

    /**
     * Contact Us Page for this controller.
     * @author 23 Digital
     */
    public function contact($slug = '')
    {
        //   pr($slug);
        $this->db->order_by('sort', 'ASC');
        $faq = $this->app_model->find('faq f', 'all', 'f.*', array('f.is_active' => 1));
        //pr($this->db->last_query());
        //pr($faq);
        $this->data['faq'] = $faq;

        $this->data['page'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'contact-us', 'p.status' => 1));

        $page_banner = $this->app_model->find('configs c', 'first', '', array('c.key' => 'contact_banner'));
        $this->data['page']['banner_image'] = $page_banner['value'];
        $this->data['is_cms'] = 1;
        //$this->data['page'] = $this->app_model->find('pages p','first','',array('p.slug'=>'contact-us','p.status' => 1));
        $this->template->write('title', "Contact Us" . " - " . COMPANY_NAME);
        $this->template->write('meta_description', 'Wellness Styles' . " - " . COMPANY_TAGLINE);
        $this->template->write_view('content', 'front/page/contact', $this->data);
        $this->template->render();
    }

    public function contact_us_save()
    {
        if($this->input->is_ajax_request())
        {
            $req_data = $this->input->post();
            unset($req_data['g-recaptcha-response']);
            $req_data['rooms'] = json_encode($req_data['rooms']);
            $temp_rooms = json_decode($req_data['rooms']);
            $total_rooms = $temp_rooms[0];

            $array = array();
            $num_of_adult = 0;
            $num_of_chiled = 0;

            for($i = 1; $i < count($temp_rooms); $i++)
            {
                $array[$i] = (array) $temp_rooms[$i];
            }
            foreach($array as $key => $value)
            {

                foreach($value as $key => $value1)
                {
                    if($key == "adult")
                    {
                        foreach($value1 as $adult)
                        {
                            $num_of_adult+= $adult;
                        }
                    }
                    if($key == "child")
                    {
                        foreach($value1 as $child)
                        {
                            $num_of_chiled+= $child;
                        }
                    }
                }
            }
            $req_data['date_of_traveling'] = $req_data['datefilter'];
            $d = $req_data['date_of_traveling'];
            unset($req_data['datefilter']);
            $client_name = $req_data['client_name'];
            $email = $req_data['email'];
            $property_name = $req_data['property_name'];
            $date_of_travel = $req_data['date_of_traveling'];
            $msg = $req_data['message'];
            $v = explode(" ", $d);
            unset($req_data['date_of_traveling']);
            $req_data['date_of_traveling_from'] = date("Y-m-d", strtotime($v[0]));
            $req_data['date_of_traveling_to'] = date("Y-m-d", strtotime($v[2]));
            $req_data['total_num_rooms'] = $total_rooms;
            $req_data['total_num_adult'] = $num_of_adult;
            $req_data['total_num_child'] = $num_of_chiled;
            $req_data['datetime_created'] = date('Y-m-d H:i:s');
            if(isset($req_data) && !empty($req_data))
            {
                unset($req_data['rooms']);
                $save = $this->app_model->save('contact_us', $req_data);
                //exit($this->db->last_query());
//            pr($save);die;
                if(isset($save) && !empty($save))
                {
                    $emailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'EC7'));
                    if(isset($emailTemplate) && !empty($emailTemplate))
                    {
                        $searchArray = array('[Name]');
                        $replaceArray = array($client_name);
                        $emailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $emailTemplate['email_template']);
                        $this->send_email($email, $emailTemplate['email_subject'], $emailTemplate['email_template'], $emailTemplate['email_from']);
                    }
                    $userData = $this->app_model->find('contact_us cus', 'first', 'cus.*', array('cus.id' => $save));
                    $emailTemplate1 = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'EC8'));
                    if(isset($emailTemplate1) && !empty($emailTemplate1))
                    {
                        $searchArray = array('[Client Name]', '[Email Address]', '[Property Name]', '[DatesTravelFrom]',
                            '[DatesTravelTo]', ' [ No of rooms]', '[Adults]', '[Children]', '[Message]');
                        $replaceArray = array($userData['client_name'], $userData['email'], $userData['property_name'],
                            $userData['date_of_traveling_from'], $userData['date_of_traveling_to'], $userData['total_num_rooms'],
                            $userData['total_num_adult'], $userData['total_num_child'], $userData['message']);
                        $emailTemplate1['email_template'] = str_replace($searchArray, $replaceArray, $emailTemplate1['email_template']);
                        $this->send_email(email, $emailTemplate1['email_subject'], $emailTemplate1['email_template'], $emailTemplate1['email_from']);
                    }
                    $data['success'] = "Information added successfully";
                }
                else
                {
                    $date['error'] = "Invalid request";
                }
            }
            echo json_encode($data);
            die;
        }
    }

    /**
     * CMS Pages Page for this controller.
     * @author 23 Digital
     */
    public function page($slug = '')
    {
        $slug = str_replace('cms-', '', $slug);
        $this->data['page'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => $slug, 'p.status' => 1));
        $page_banner = $this->app_model->find('configs c', 'first', '', array('c.key' => 'cms_page_banner'));
        $this->data['page']['banner_image'] = $page_banner['value'];
        //pr($this->data['page']);
        $this->data['is_cms'] = 1;
        $this->data['footer_active_menu'] = $slug;
        if(isset($this->data['page']) && !empty($this->data['page']['title']))
        {
            $this->template->write('title', $this->data['page']['title'] . " - " . COMPANY_NAME);
            $this->template->write('meta_description', 'Wellness Styles' . " - " . COMPANY_TAGLINE);
            $this->template->write_view('content', 'front/home/page', $this->data);
            $this->template->render();
        }
        else
        {
            redirect(base_url());
        }
    }

    public function login_mobile()
    {
        $userData = $this->session->userdata('customer');
        if(isset($_SERVER['HTTP_REFERER']))
        {
            $this->session->set_userdata('REDIRECT_URL', $_SERVER['HTTP_REFERER']);
            $this->session->set_userdata('REDIRECT_URL_MOBILE', $_SERVER['HTTP_REFERER']);
        }
        if(isset($userData) && $userData != "")
        {
            redirect(base_url());
        }
        else
        {
            $this->template->write('title', "LOG IN" . " - " . COMPANY_NAME);
            $this->template->write_view('content', 'front/common/mobile/login_mobile', $this->data);
            $this->template->render();
        }
    }

    public function register_mobile()
    {
        $userData = $this->session->userdata('customer');
        //$this->data['pre_phone_list'] = $this->app_model->find('countries c', 'list', array('c.phonecode as phonecode', 'c.iso3 as codename'), array('c.is_active' => 1));
        if(isset($_SERVER['HTTP_REFERER']))
        {
            $this->session->set_userdata('REDIRECT_URL', $_SERVER['HTTP_REFERER']);
            $this->session->set_userdata('REDIRECT_URL_MOBILE', $_SERVER['HTTP_REFERER']);
        }
        if(isset($userData) && $userData != "")
        {
            redirect(base_url());
        }
        else
        {
            $this->template->write('title', 'Registration' . " - " . COMPANY_TAGLINE);
            $this->template->write('meta_description', 'Registration' . " - " . COMPANY_TAGLINE);
            $this->template->write_view('content', 'front/common/mobile/register_mobile', $this->data);
            $this->template->render();
        }
    }

    public function forget_mobile()
    {
        $userData = $this->session->userdata('customer');
        if(isset($_SERVER['HTTP_REFERER']))
        {
            $this->session->set_userdata('REDIRECT_URL', $_SERVER['HTTP_REFERER']);
        }
        if(isset($userData) && $userData != "")
        {
            redirect(base_url());
        }
        else
        {
            $this->template->write('title', "Forget Password" . " - " . COMPANY_NAME);
            $this->template->write_view('content', 'front/common/mobile/forget_mobile', $this->data);
            $this->template->render();
        }
    }

    public function facebook_login_mobile()
    {
        $userData = $this->session->userdata('customer');
        if(isset($userData) && $userData != "")
        {
            redirect(base_url());
        }
        else
        {
            $this->template->write('title', "Facebook Update Password" . " - " . COMPANY_NAME);
            $this->template->write_view('content', 'front/common/mobile/fb_login_mobile', $this->data);
            $this->template->render();
        }
    }

    public function google_login_mobile()
    {
        $userData = $this->session->userdata('customer');
        if(isset($userData) && $userData != "")
        {
            redirect(base_url());
        }
        else
        {
            $this->template->write('title', "Google Update Password" . " - " . COMPANY_NAME);
            $this->template->write_view('content', 'front/common/mobile/google_login_mobile', $this->data);
            $this->template->render();
        }
    }

    public function profile_mobile()
    {
        if($this->session->has_userdata('customer'))
        {
            $customer = $this->session->userdata('customer');
            // $this->data['pre_phone_list'] = $this->app_model->find('countries c', 'list', array('c.phonecode as phonecode', 'c.iso3 as codename'), array('c.is_active' => 1));
            $this->data['customer'] = $customer;
            $this->template->write('title', "Edit Profile" . " - " . COMPANY_NAME);
            $this->template->write_view('content', 'front/common/mobile/profile_mobile', $this->data);
            $this->template->render();
        }
        else
        {
            redirect('/');
        }
    }

    public function billing_mobile()
    {

        $userData = $this->session->userdata('customer');
        if(isset($userData) && $userData != "")
        {
            $this->data['session'] = $userData;
            $this->data['customer'] = $userData;
            $this->db->order_by('c.country_name', 'ASC');
            $this->data['country_list'] = $this->app_model->find('countries c', 'list', array('c.iso2 as country_id', 'c.country_name'), array('c.is_active' => 1));

            if(isset($userData['country_id']) && $userData['country_id'] != "")
            {
                $this->db->order_by('s.state_name', 'ASC');
                $this->data['state_list'] = $this->app_model->find('states s', 'list', array('s.iso2 as state_id', 's.state_name'), array('s.country_iso2' => $userData['country_id']));
            }
            else
            {
                $this->data['state_list'] = "";
            }
            if(isset($userData['state_id']) && $userData['state_id'] != "")
            {
                $country_id = $this->data['country_list_ids'][$userData['country_id']];
                $this->db->order_by('c.city_name', 'ASC');
                $this->data['city_list'] = $this->app_model->find('cities c', 'list', array('c.city_id', 'c.city_name'), array('c.country_id' => $country_id, 'c.state_iso2' => $userData['state_id']));
            }
            else
            {
                $this->data['city_list'] = "";
            }
            $country = $this->app_model->find('countries c', 'first', array('c.iso2 as country_id', 'c.country_name'), array('c.iso2' => $userData['country_id']));
            $this->data['country_name'] = $country['country_name'];

            if(isset($country_id))
                $this->db->where('s.country_id', $country_id);
            $state = $this->app_model->find('states s', 'first', array('s.iso2 as state_id', 's.state_name'), array('s.iso2' => $userData['state_id']));
            $this->data['state_name'] = $state['state_name'];

            if(isset($country_id))
                $this->db->where('c.country_id', $country_id);

            $city = $this->app_model->find('cities c', 'first', array('c.city_id', 'c.city_name'), array('c.city_id' => $userData['city_id']));
            $this->data['city_name'] = $city['city_name'];

            $this->chk_session();
            $this->template->write('title', "Edit Billing" . " - " . COMPANY_NAME);
            $this->template->write_view('content', 'front/common/mobile/billing_mobile', $this->data);
            $this->template->render();
        } else
        {
            redirect('/');
        }
    }

    public function checkout($id = null)
    {
        $ids = decrypt($id);
        $get_specific_id = explode('#', $ids);
        $result = 1;

        // pr($get_specific_id) ;

        if($this->session->has_userdata('customer'))
        {
            $this->data['userData'] = $this->session->userdata('customer');
            //pr($this->data['userData']) ;
        }
        if($this->session->has_userdata('postData'))
        {
            //$this->session->unset_userdata('postData');
        }
        if(isset($get_specific_id) && !empty($get_specific_id))
        {
            $room_id = $get_specific_id[0];
            $hotel_id = $get_specific_id[1];
            $roomTypeCode = $get_specific_id[2];
        }

        $roomList = $this->session->userdata('roomList');
        $searchData = $this->session->userdata('searchData');
        $postData = $this->input->post();
        //pr($postData) ;
        $roomAvai = $this->call_ean_api_rooms_method2('avail', selected_currency, $hotel_id, $locale = 'en_US', $searchData['date1'], $searchData['date2'], $searchData['rooms'], $roomTypeCode, $room_id);
        $roomData = json_decode($roomAvai, true);
        $this->data['roomData'] = $roomData;
        //pr($this->data['roomData']) ;

        if(isset($roomAvai) && !empty($roomAvai))
        {
            $roomData = array();
            $roomObject = json_decode($roomAvai, true);
            $this->data['roomObject'] = $roomObject;
            if(isset($roomObject['HotelRoomAvailabilityResponse']['EanWsError']) && !empty($roomObject['HotelRoomAvailabilityResponse']['EanWsError']))
            {
                $this->session->set_flashdata('error', $roomObject['HotelRoomAvailabilityResponse']['EanWsError']['presentationMessage']);
                redirect('/');
            }
            else if($roomObject['HotelRoomAvailabilityResponse']['@size'] <= 1)
            {
                $roomList = $roomObject['HotelRoomAvailabilityResponse']['HotelRoomResponse'];
                $roomData[$roomList['rateCode']] = $roomList;
            }
            else
            {
                foreach($roomObject['HotelRoomAvailabilityResponse']['HotelRoomResponse'] as $roomList)
                {
                    $roomData[$roomList['rateCode']] = $roomList;
                }
            }
        }
        //pr($roomData) ;
        //$roomData = $this->session->userdata ( 'roomData' );
        $expediaData = $this->data['roomObject'];
        $this->data['roomlist'] = $roomData;

        $room_specfic_checkout = isset($roomData[$room_id]) ? $roomData[$room_id] : '';
        $expedia = isset($expediaData['HotelRoomAvailabilityResponse']) ? $expediaData['HotelRoomAvailabilityResponse'] : '';
        $searchData = $this->session->userdata('searchData');
        $this->data['detail'] = $this->call_ean_single('info', selected_currency, $hotel_id, 'en_US', '1');
        $this->data['expediaData'] = json_decode($this->data['detail'], true);



        $this->data['hotelInfo'] = $this->data['expediaData']['HotelInformationResponse']['HotelSummary'];
        $this->db->order_by('images.is_main', 'DESC');
        $images = $this->app_model->find('hotel_images images', 'all', 'images.*', array('images.EANHotelID' => $hotel_id));
        $this->data['images'] = $images;
        if(empty($room_specfic_checkout) && empty($expedia) && empty($searchData))
        {
            $this->session->set_flashdata('error', search_checkout);
            redirect('/');
        }
        else
        {
            $this->data['room_specfic_checkout'] = $room_specfic_checkout;
            $this->data['expedia'] = $expedia;
            $this->data['searchData'] = $searchData;
        }


        //pr($postData) ;
        if(isset($postData) && !empty($postData) && !isset($postData['currency_selector']))
        {
            //pr($postData) ;
            $orderData = $postData;
            if(isset($orderData['room_data']) && !empty($orderData['room_data']))
            {
                $orderData['room_data'] = base64_decode($orderData['room_data']);
            }
            unset($postData['password']);
            unset($postData['confirm_password']);

            if($this->session->has_userdata('postData'))
            {
                $this->session->unset_userdata('postData');
            }


            //unset($postData['firstname_2']) ;
            //unset($postData['lastname_2']) ;
            $postData['departureDate'] = $searchData['date2'];
            $postData['rooms'] = $searchData['rooms'];
            if(isset($this->data['userData']) && !empty($this->data['userData']))
            {
                $postData['email'] = $this->data['userData']['email'];
                $orderData['email'] = $this->data['userData']['email'];
            }

            $apiKey = apiKey;
            $secret = secret;
            $timestamp = gmdate('U');
            $sig = md5($apiKey . $secret . $timestamp);


            $token = getToken(8);
            $time = time();
            $confirmation_id = md5($hotel_id . $time . $token . $hotel_id . $sig);
            $postData['confirmation_id'] = $confirmation_id;
            $postData['session_id'] = $this->data['session_token'];

            $orderData['confirmation_id'] = $confirmation_id;
            $orderData['session_id'] = $this->data['session_token'];

            $bookingData = $this->book_reservation_ean($postData);
            //pr($bookingData) ;
            if(!empty($bookingData))
            {
                $booking = json_decode($bookingData, true);
                //pr($booking) ;
                if(isset($postData['state_id_text']))
                {
                    $orderData['state_id'] = $postData['state_id'];
                    $orderData['state_id_text'] = $postData['state_id_text'];
                    $orderData['city_id'] = $postData['city_id'];
                    $orderData['city_id_text'] = $postData['city_id_text'];
                }
                $this->data['postData'] = $orderData;

                $this->session->set_userData('postData', $orderData);
                if(isset($booking['HotelRoomReservationResponse']['EanWsError']) && !empty($booking['HotelRoomReservationResponse']['EanWsError']) )
                {
                    if($booking['HotelRoomReservationResponse']['EanWsError']['handling']!='AGENT_ATTENTION')
                    {
                        // pr($booking['HotelRoomReservationResponse']['EanWsError']) ;
                        $this->session->set_flashdata('error', $booking['HotelRoomReservationResponse']['EanWsError']['presentationMessage']);
                        redirect(base_url() . 'checkout/' . $id);
                    }

                    $this->data['postData'] = $orderData;
                    if(!isset($this->data['userData']) && empty($this->data['userData']))
                    {
                        $user = array();
                        $user['email'] = $orderData['email'];
                        $user['password'] = md5($orderData['password']);
                        $user['first_name'] = $orderData['room1firstName'];
                        $user['last_name'] = $orderData['room1lastName'];
                        $user['role_id'] = 3;
                        $user['is_active'] = 1;
                        $user_id = $this->app_model->save('users', $user);

                        $customer['user_id'] = $user_id;
                        $customer['address1'] = $orderData['address1'];
                        $customer['country_id'] = $orderData['countryCode'];
                        $customer['post_code'] = $orderData['postalCode'];

                        if(isset($orderData['state_id']))
                            $customer['state_id'] = $orderData['state_id'];
                        if(isset($orderData['city_id']))
                            $customer['city_id'] = $orderData['city_id'];

                        if(!in_array($orderData['countryCode'],$this->data['countryArr']))
                        {
                            $customer['state_id'] = $orderData['state_id_text'];
                            $customer['city_id'] = $orderData['city_id_text'];
                        }
                        $customer['pre_phone'] = $orderData['pre_phone'];
                        $customer['phone'] = $orderData['workPhone'];
                        $customer_id = $this->app_model->save('customers', $customer);
                        $userData = $this->app_model->find('users u', 'first', array('u.*', 'g.file_name', 'CONCAT(u.first_name," ",u.last_name) as name', 'customer.id as customer_id',
                                'customer.user_id as customer_user_id', 'customer.address1', 'customer.address2',
                                'customer.country_id', 'customer.state_id', 'customer.city_id', 'customer.post_code',
                                'customer.phone', 'customer.pre_phone')
                            , array('u.id' => $user_id), array(
                                array('customers customer', 'customer.user_id=u.id', 'LEFT'),
                                array('gallery g', 'g.user_id=u.id AND g.is_profile=1', 'LEFT')
                            )
                        );
                        $this->data['userData'] = $userData;
                        $this->session->set_userdata('customer', $userData);
                    }

                    $orderData['customer_id'] = $this->data['userData']['id'];
                    $orderData['departureDate'] = $searchData['date2'];
                    $orderData['rooms'] = $searchData['rooms'];
                    $itnearyId = $booking['HotelRoomReservationResponse']['EanWsError']['itineraryId'];
                    $statusResponse = $this->check_ean_booking_status('itin', selected_currency, $itnearyId, 'en_US', $orderData['email']);
                    $response = json_decode($statusResponse, true);
                    if(isset($response['HotelItineraryResponse']['Itinerary']['HotelConfirmation']['status']) && ($response['HotelItineraryResponse']['Itinerary']['HotelConfirmation']['status'] == 'PS' || $response['HotelItineraryResponse']['Itinerary']['HotelConfirmation']['status'] == 'CF' ))
                    {
                        $ps = array();
                        $ps['customer_id'] = $orderData['customer_id'];
                        $ps['itineraryId'] = $itnearyId;
                        $ps['email'] = $orderData['email'];
                        $ps['selected_currency'] = selected_currency;
                        $ps['order_data'] = json_encode($orderData);
                        $ps['search_data'] = json_encode($searchData);
                        // $ps['customer_data'] = json_encode($this->data['userData']); // ITL-46
                        $ps['hotel_response'] = $this->data['detail'];
                        $this->app_model->save('pending_booking', $ps);
                        $link = base64_encode($itnearyId . '-' . 'pending');
                        redirect(base_url() . 'success/' . $link);
                    }
                    else
                    {
                        $this->session->set_flashdata('error', $booking['HotelRoomReservationResponse']['EanWsError']['presentationMessage']);
                        redirect(base_url() . 'checkout/' . $id);
                    }
                }

                $customer = array();
                if(!isset($this->data['userData']) && empty($this->data['userData']))
                {
                    $user = array();
                    $user['email'] = $orderData['email'];
                    $user['password'] = md5($orderData['password']);
                    $user['first_name'] = $orderData['room1firstName'];
                    $user['last_name'] = $orderData['room1lastName'];
                    $user['role_id'] = 3;
                    $user['is_active'] = 1;
                    $user_id = $this->app_model->save('users', $user);

                    $customer['user_id'] = $user_id;
                    $customer['address1'] = $orderData['address1'];
                    $customer['country_id'] = $orderData['countryCode'];
                    $customer['post_code'] = $orderData['postalCode'];
                    if(isset($orderData['state_id']))
                        $customer['state_id'] = $orderData['state_id'];

                    if(isset($orderData['city_id']))
                        $customer['city_id'] = $orderData['city_id'];

                    if(!in_array($orderData['countryCode'],$this->data['countryArr']))
                    {
                        $customer['state_id'] = $orderData['state_id_text'];
                        $customer['city_id'] = $orderData['city_id_text'];
                    }

                    $customer['phone'] = $orderData['workPhone'];
                    $customer['pre_phone'] = $orderData['pre_phone'];
                    $customer_id = $this->app_model->save('customers', $customer);
                }
                else
                {
                    //pr($this->data['userData']);
                    $customer['id'] = $this->data['userData']['customer_id'];
                    $customer['phone'] = $orderData['workPhone'];
                    $customer['address1'] = $orderData['address1'];
                    $customer['country_id'] = $orderData['countryCode'];
                    if(isset($orderData['state_id']))
                        $customer['state_id'] = $orderData['state_id'];
                    if(isset($orderData['city_id']))
                        $customer['city_id'] = $orderData['city_id'];

                    if(!in_array($orderData['countryCode'],$this->data['countryArr']))
                    {
                        $customer['state_id'] = $orderData['state_id_text'];
                        $customer['city_id'] = $orderData['city_id_text'];
                    }

                    $customer['post_code'] = $orderData['postalCode'];
                    $customer['pre_phone'] = $orderData['pre_phone'];
                    $customer['phone'] = $orderData['workPhone'];
                    $customer['pre_phone'] = $orderData['pre_phone'];
                    $customer_id = $this->app_model->save('customers', $customer);
                    $user_id = $this->data['userData']['id'];
                }

                $userData = $this->app_model->find('users u', 'first', array('u.*', 'g.file_name', 'CONCAT(u.first_name," ",u.last_name) as name', 'customer.id as customer_id',
                        'customer.user_id as customer_user_id', 'customer.address1', 'customer.address2',
                        'customer.country_id', 'customer.state_id', 'customer.city_id', 'customer.post_code', 'customer.pre_phone',
                        'customer.phone')
                    , array('u.id' => $user_id), array(
                        array('customers customer', 'customer.user_id=u.id', 'LEFT'),
                        array('gallery g', 'g.user_id=u.id AND g.is_profile=1', 'LEFT')
                    )
                );
                $this->data['userData'] = $userData;
                $this->session->set_userdata('customer', $userData);

                //pr($booking['HotelRoomReservationResponse']['EanWsError']) ;
                if(isset($booking['HotelRoomReservationResponse']['EanWsError']) && !empty($booking['HotelRoomReservationResponse']['EanWsError']))
                {
                    $this->session->set_flashdata('error', $booking['HotelRoomReservationResponse']['EanWsError']['presentationMessage']);
                }
                else
                {
//                    $statusResponse = $this->check_ean_booking_status('itin', selected_currency, $booking['HotelRoomReservationResponse']['itineraryId'], 'en_US', $orderData['email']);
//                    $response = json_decode($statusResponse, true);
//                    pr($response) ;
//
//                    if((isset($response['HotelItineraryResponse']['Itinerary']['HotelConfirmation'][0]['status']) || isset($response['HotelItineraryResponse']['Itinerary']['HotelConfirmation']['status'])) && ($response['HotelItineraryResponse']['Itinerary']['HotelConfirmation'][0]['status'] == 'PS' || ($response['HotelItineraryResponse']['Itinerary']['HotelConfirmation'][0]['status'] == 'CF' || $response['HotelItineraryResponse']['Itinerary']['HotelConfirmation']['status'] == 'CF' )))
//                    {
//
//                        $ps = array();
//                        $ps['customer_id'] = $user_id;
//                        $ps['itineraryId'] = $booking['HotelRoomReservationResponse']['itineraryId'];
//                        $ps['email'] = $orderData['email'];
//                        $ps['selected_currency'] = selected_currency;
//                        $ps['order_data'] = json_encode($orderData);
//                        $ps['search_data'] = json_encode($searchData);
//                        $ps['customer_data'] = json_encode($this->data['userData']);
//                        $ps['hotel_response'] = $this->data['detail'];
//                        $this->app_model->save('pending_booking', $ps);
//                        $link = base64_encode($itnearyId . '-' . 'pending');
//                        //redirect(base_url() . 'success/' . $link);
//                    }

                    $bookingResponse = array();
                    $booking = $booking['HotelRoomReservationResponse'];
                    $bookingResponse['confirmationNumbers'] = json_encode($booking['confirmationNumbers']);
                    $bookingResponse['itineraryId'] = $booking['itineraryId'];
                    $bookingResponse['reservationStatusCode'] = $booking['reservationStatusCode'];
                    $bookingResponse['hotel_id'] = $orderData['hotelId'];
                    $bookingResponse['special_request'] = $orderData['message'];
                    $bookingResponse['booking_phone'] = $orderData['pre_phone'] . ' ' . $orderData['workPhone'];
                    $bookingResponse['customer_id'] = $user_id; // save user_id from user table
                    $bookingResponse['taxes'] = json_encode($orderData['taxes']);
                    $bookingResponse['no_of_days'] = json_encode($orderData['no_of_days']);
                    $bookingResponse['rate_per_day'] = json_encode($orderData['rate_per_day']);
                    $bookingResponse['checkout_currency'] = json_encode($orderData['checkout_currency']);
                    $bookingResponse['booking_response'] = $bookingData;
                    $bookingResponse['hotel_response'] = $this->data['detail'];
                    $bookingResponse['room_response'] = isset($orderData['room_data']) ? $orderData['room_data'] : '';
                    // $bookingResponse['customer_data'] = json_encode($orderData); // ITL-46
                    $numberOfAdults = 0;
                    $numberOfChildren = 0;
                    if(isset($booking['RateInfos']['RateInfo']['RoomGroup']['Room']))
                    {
                        if($booking['numberOfRoomsBooked'] > 1)
                        {
                            foreach($booking['RateInfos']['RateInfo']['RoomGroup']['Room'] as $room):
                                $numberOfAdults+=isset($room['numberOfAdults']) ? $room['numberOfAdults'] : 0;
                                $numberOfChildren+=isset($room['numberOfChildren']) ? $room['numberOfChildren'] : 0;
                            endforeach;
                        }
                        else
                        {
                            $numberOfAdults+=isset($booking['RateInfos']['RateInfo']['RoomGroup']['Room']['numberOfAdults']) ? $booking['RateInfos']['RateInfo']['RoomGroup']['Room']['numberOfAdults'] : 0;
                            $numberOfChildren+=isset($booking['RateInfos']['RateInfo']['RoomGroup']['Room']['numberOfChildren']) ? $booking['RateInfos']['RateInfo']['RoomGroup']['Room']['numberOfChildren'] : 0;
                        }
                    }




                    $bookingResponse['numberOfAdults'] = $numberOfAdults;
                    $bookingResponse['numberOfChildren'] = $numberOfChildren;

                    if($booking['RateInfos']['@size'] == 1)
                    {
                        $bookingResponse['total'] = $booking['RateInfos']['RateInfo']['ChargeableRateInfo']['@total'];
                        $bookingResponse['currency'] = $booking['RateInfos']['RateInfo']['ChargeableRateInfo']['@currencyCode'];
                        $bookingResponse['nonRefundable'] = $booking['RateInfos']['RateInfo']['nonRefundable'];
                    }
                    $bookingResponse['arrivalDate'] = date('Y-m-d', strtotime($booking['arrivalDate']));
                    $bookingResponse['departureDate'] = date('Y-m-d', strtotime($booking['departureDate']));
                    $order_id = $this->app_model->save('orders', $bookingResponse);

                    $other = array();
                    if(isset($booking['confirmationNumbers']) && is_array($booking['confirmationNumbers']))
                    {
                        foreach($booking['confirmationNumbers'] as $confirmation)
                        {
                            $other['confirmationNumbers'] = $confirmation;
                            $other['order_id'] = $order_id;
                            $other['itineraryId'] = $booking['itineraryId'];
                            $this->app_model->save('order_room_specific_status', $other);
                        }
                    }
                    else
                    {
                        $other['confirmationNumbers'] = $booking['confirmationNumbers'];
                        $other['order_id'] = $order_id;
                        $other['itineraryId'] = $booking['itineraryId'];
                        $this->app_model->save('order_room_specific_status', $other);
                    }

                    $postData = array();
                    unset($postData);
                    $this->data['postData'] = array();
                    $link = base64_encode($booking['itineraryId'] . '-' . $order_id);
                    redirect(base_url() . 'success/' . $link);
                }
            }

            $this->data['postData'] = $orderData;
        }


        if($this->session->has_userdata('postData'))
        {
            if(empty($this->data['postData']))
            {
                $this->data['postData'] = $this->session->userdata('postData');
            }
            //$this->session->unset_userdata('postData');
        }


        $this->data['countryList'] = $this->get_country_list_iso2();


        if((isset($this->data['userData']['country_id']) && $this->data['userData']['country_id'] != "") || (isset($this->data['postData']) && !empty($this->data['postData']['countryCode'])))
        {

            if(isset($this->data['postData']) && !empty($this->data['postData']['countryCode']))
            {
                $this->data['userData']['country_id'] = $this->data['postData']['countryCode'];
            }
            $country_id = $this->data['country_list_ids'][$this->data['userData']['country_id']];
            if(isset($country_id))
                $this->db->where('s.country_id', $country_id);

            $this->db->order_by('s.state_name', 'ASC');
            $this->data['state_list'] = $this->app_model->find('states s', 'list', array('s.iso2 as state_id', 's.state_name'), array('s.country_iso2' => $this->data['userData']['country_id']));
        } else
        {
            $this->data['state_list'] = array();
        }

        if((isset($this->data['userData']['state_id']) && $this->data['userData']['state_id'] != "") || (isset($this->data['postData']) && !empty($this->data['postData']['state_id'])))
        {
            if((isset($this->data['postData']) && !empty($this->data['postData']['state_id'])))
            {
                $this->data['userData']['state_id'] = $this->data['postData']['state_id'];
            }

            if(isset($country_id))
                $this->db->where('c.country_id', $country_id);

            $this->db->order_by('c.city_name', 'ASC');
            $this->data['city_list'] = $this->app_model->find('cities c', 'list', array('c.city_id', 'c.city_name'), array('c.state_iso2' => $this->data['userData']['state_id']));
        } else
        {
            $this->data['city_list'] = array();
        }

        $this->data['hotel_id'] = $hotel_id;
        $this->data['paymentinfo'] = $this->call_payment_info($hotel_id);
        //pr($this->data['paymentinfo']) ;
        if($result == 1)
        {
            $this->template->write('title', "Checkout" . " - " . COMPANY_NAME);
            $this->template->write('meta_description', 'Checkout' . " - " . COMPANY_TAGLINE);
            $this->template->write('meta_keywords', 'Checkout' . " - " . COMPANY_TAGLINE);
            $this->template->write_view('content', 'front/home/checkout', $this->data);
            $this->template->render();
        }
    }

    /**
     * @param null $id
     */
    public function wellness_package_booking($hotel_id = null,$wellness_package_id = null) {
        $this->send_email($recipient, $intEmailTemplate['email_subject'], $intEmailTemplate['email_template'], $intEmailTemplate['email_from']);

        exit;
        $this->data['hotel_id'] = $hotel_id;
        $this->data['wellness_package_id'] = $wellness_package_id;

        if(isset($hotel_id) && !empty($hotel_id) && isset($wellness_package_id) && !empty($wellness_package_id)) {
            $this->db->where('hs.EANHotelID', $hotel_id);
            $query = $this->app_model->find('hotels_select hs', 'all', array('hs.*'), array('hs.is_active' => 1));

            if(count($query) > 0) {
                $this->data['property_name'] = $query[0]["Name"];
                $this->data['complete_address'] = get_address_from_expedia($query[0]);
            }

            $activities = $this->get_hotels_style($hotel_id);
            $this->data['activities'] = $activities;

            $this->data['countryList'] = $this->get_country_list_iso2();
        }

        // Added by Josh Curtis on 28 July 2020 to populate property image from our own library
        if(isset($hotel_id) && !empty($hotel_id)) {
            $this->db->order_by('images.is_main', 'DESC');
            $images = $this->app_model->find('hotel_images images', 'all', 'images.*', array('images.EANHotelID' => $hotel_id));
            $this->data['hotelPreviewImage'] = $images[0]['URL'];
        }

        $postData = $this->input->post(NULL, TRUE);
        //var_dump($postData);

        if(isset($postData) && !empty($postData)) {
            // Send off notification emails to internal contacts and client

            $age_agree = (isset($postData['age_accept']) && !empty($postData['age_accept'])) ? $postData['age_accept'] : "No";
            if($age_agree == 1) { $age_agree = "Yes"; }
            $tc_agree = (isset($postData['tc_accept']) && !empty($postData['tc_accept'])) ? $postData['tc_accept'] : "No";
            if($tc_agree == 1) { $tc_agree = "Yes"; }

            // Source who we're sending to internally
            //$intRecipients = array('support@inthislifewellnesstravel.com','josh@jcdm.net');
            $intRecipients = array('sankarnandi1010@gmail.com','josh@jcdm.net');
//            $intRecipients = array('josh@jcdm.net');
            // Load property email address
            if(isset($this->data['activities']['wp_property_email']) && !empty($this->data['activities']['wp_property_email'])) {
                $intRecipients[] = $this->data['activities']['wp_property_email'];
            }
            // Remove duplicates so we're not double-sending
            $intRecipients = array_unique($intRecipients);

            // Source the client's email
            $extRecipient = $postData['email'];

            // Prep the data fields
            $bookerCategory = (isset($postData['bookerCategory']) && !empty($postData['bookerCategory'])) ? $postData['bookerCategory'] : "Not supplied";
            $contactName = (isset($postData['contactName']) && !empty($postData['contactName'])) ? $postData['contactName'] : "Not supplied";
            $email = (isset($postData['email']) && !empty($postData['email'])) ? $postData['email'] : "Not supplied";
            $phone = (isset($postData['workPhone']) && !empty($postData['workPhone'])) ? $postData['pre_phone'] . " " . $postData['workPhone'] : "Not supplied";
            $billing_address = (isset($postData['billing_address']) && !empty($postData['billing_address'])) ? $postData['billing_address'] : "Not supplied";
            $country = (isset($postData['countryCode']) && !empty($postData['countryCode'])) ? $this->data['countryList'][$postData['countryCode']] : "Not supplied";
            $state = (isset($postData['state_id']) && !empty($postData['state_id'])) ? $this->get_state_base_id($postData['state_id']) : "Not supplied";
            $citySuburb = (isset($postData['citySuburb']) && !empty($postData['citySuburb'])) ? $postData['citySuburb'] : "Not supplied";
            $postcode = (isset($postData['postalCode']) && !empty($postData['postalCode'])) ? $postData['postalCode'] : "Not supplied";
            $checkIn = (isset($postData['date1']) && !empty($postData['date1'])) ? $postData['date1'] : "Not supplied";
            $checkOut = (isset($postData['date2']) && !empty($postData['date2'])) ? $postData['date2'] : "Not supplied";
            $firstName = (isset($postData['firstName']) && !empty($postData['firstName'])) ? $postData['firstName'] : "Not supplied";
            $lastName = (isset($postData['lastName']) && !empty($postData['lastName'])) ? $postData['lastName'] : "Not supplied";

//            $numberTravellers = (isset($postData['rooms']) && !empty($postData['rooms'])) ? $postData['rooms'] : "Not supplied";

            // Prepare room listing for travellers
            if(isset($postData['rooms']) && count($postData['rooms']) > 1) {
                $roomList = array();
                $roomCount = 0;
                $travelerCount = 0;
                foreach($postData['rooms'] as $key => $room) {
                    if($key != 0) {
                        $roomCount++;
                        $roomList[] = "Room " . $roomCount;
                        $travelerCount = $travelerCount + $room['adult'][0];
                        $roomList[] = "Adults (18+): " . $room['adult'][0];
                        $travelerCount = $travelerCount + $room['child'][0];
                        $roomList[] = "Children (0-17): " . $room['child'][0];
                        foreach ($room['child_age'] as $childAge) {
                            $roomList[] = "Age: " . $childAge;
                        }
                        $roomList[] = "";
                    }
                }
                $roomList[] = "Total travellers: " . $travelerCount;
//                $roomList[] = "<pre>" . print_r($postData['rooms'],1) . "</pre>";
                $numberTravellers = implode("<br>",$roomList);
            } else {
                $numberTravellers = "Information not supplied";
            }
            // End prepare room listing for travellers

            $roomType = (isset($postData['roomType']) && !empty($postData['roomType'])) ? $postData['roomType'] : "Not supplied";
            $beddingRequest = (isset($postData['beddingRequest']) && !empty($postData['beddingRequest'])) ? $postData['beddingRequest'] : "None made";
            $otherRequests = (isset($postData['otherRequests']) && !empty(trim($postData['otherRequests']))) ? "<br>" . nl2br(trim($postData['otherRequests'])) : "None made";
            $packageName = $this->data['activities']['wp'.$this->data['wellness_package_id'].'_name'];
            $packageDescription = $this->data['activities']['wp'.$this->data['wellness_package_id'].'_description'];
            $packagePrice = nl2br($this->data['activities']['wp'.$this->data['wellness_package_id'].'_price']);

            // Swap out the placeholders
            $searchArray = array('[Category Traveller]','[Contact Name]','[Billing Address]','[First Name]','[Last Name]','[Email]','[Phone]','[Country]','[State]','[City]','[Postcode]','[Property Name]','[Property Address]','[Package title]','[Package Description]','[Package Price]','[Number Travellers]','[Check in]','[Check out]','[Room Type]','[Bedding Request]','[Other Requests]','[Age Agree]','[Terms and Conditions Agree]');
            $replaceArray = array($bookerCategory,$contactName,$billing_address,$firstName,$lastName,$email,$phone,$country,$state,$citySuburb,$postcode,$this->data['property_name'],$this->data['complete_address'],$packageName,$packageDescription,$packagePrice,$numberTravellers,$checkIn,$checkOut,$roomType,$beddingRequest,$otherRequests,$age_agree,$tc_agree);

            // Send ITL and the property their copies of the email
            $intEmailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'WP-BOOK-INTERNAL'));
            if(isset($intEmailTemplate) && !empty($intEmailTemplate)) {

                $intEmailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $intEmailTemplate['email_template']);

                // Send the email
                foreach($intRecipients as $recipient) {
                    $this->send_email($recipient, $intEmailTemplate['email_subject'], $intEmailTemplate['email_template'], $intEmailTemplate['email_from']);
                }
            }

            // Send the client their email
            $extEmailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'WP-BOOK-CLIENT'));
            if(isset($extEmailTemplate) && !empty($extEmailTemplate)) {
                $extEmailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $extEmailTemplate['email_template']);
                // Send the email
                $this->send_email($extRecipient, $extEmailTemplate['email_subject'], $extEmailTemplate['email_template'], $extEmailTemplate['email_from']);
            }

            // Set success message for user
            $this->data['successMessage'] = "<div class=\"alert alert-success\"><h4>Thank you for your booking.</h4><p>Your Wellness Package has now been requested to the hotel. You will receive confirmation via email in the next 24 - 48 hours.</p><p>If your package does not include accommodation, please ensure that you also make a reservation for your room accommodation at this hotel. <a class=\"alert-link\" href=\"/hotel_detail/" . $hotel_id . "\">Click here to return to " .  $this->data['property_name'] . " if you wish to make your reservation now.</a></p><p>Should you have any further enquiries please contact <a class=\"alert-link\" href=\"mailto:support@inthislifewellnesstravel.com\">support@inthislifewellnesstravel.com</a>.</p><p>We wish you a wonderful Wellness Experience.</p><p>The Team @ In This Life Wellness Travel</p></div>";
        }

        $this->template->write('title', "Wellness Package Booking" . " - " . COMPANY_NAME);
        $this->template->write('meta_description', 'Wellness Package Booking' . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_keywords', 'Wellness Package Booking' . " - " . COMPANY_TAGLINE);
        $this->template->write_view('content', 'front/home/wellness_package_booking', $this->data);
        $this->template->render();
    }


    public function list_your_wellness_property() {

        $this->data['page'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'list-your-wellness-property', 'p.status' => 1));

//        var_dump($this->data['page']);

        $postData = $this->input->post(NULL, TRUE);
        //var_dump($postData);

        if(isset($postData) && !empty($postData)) {
            // Send off notification emails to internal contacts and client

            // Source who we're sending to internally
            $intRecipients = array('support@inthislifewellnesstravel.com','josh@jcdm.net');
            // Load property email address

            // Source the client's email
            $extRecipient = $postData['email'];

            // Prep the data fields
            $companyName = (isset($postData['companyName']) && !empty($postData['companyName'])) ? $postData['companyName'] : "Not supplied";
            $contactName = (isset($postData['contactName']) && !empty($postData['contactName'])) ? $postData['contactName'] : "Not supplied";
            $email = (isset($postData['email']) && !empty($postData['email'])) ? $postData['email'] : "Not supplied";
            $webAddress = (isset($postData['webAddress']) && !empty($postData['webAddress'])) ? $postData['webAddress'] : "Not supplied";
            $bestDescription = (isset($postData['bestDescription']) && !empty($postData['bestDescription'])) ? $postData['bestDescription'] : "Not supplied";

            // Process tick boxes into yes/no values
            $wellnessImmersion = (isset($postData['wellnessImmersion']) && !empty($postData['wellnessImmersion'])) ? $postData['wellnessImmersion'] : "No";
            if($wellnessImmersion == 1) { $wellnessImmersion = "Yes"; }
            $wellnessLuxury = (isset($postData['wellnessLuxury']) && !empty($postData['wellnessLuxury'])) ? $postData['wellnessLuxury'] : "No";
            if($wellnessLuxury == 1) { $wellnessLuxury = "Yes"; }
            $thermalMineralSprings = (isset($postData['thermalMineralSprings']) && !empty($postData['thermalMineralSprings'])) ? $postData['thermalMineralSprings'] : "No";
            if($thermalMineralSprings == 1) { $thermalMineralSprings = "Yes"; }
            $ecoSustainable = (isset($postData['ecoSustainable']) && !empty($postData['ecoSustainable'])) ? $postData['ecoSustainable'] : "No";
            if($ecoSustainable == 1) { $ecoSustainable = "Yes"; }
            $touchOfWellness = (isset($postData['touchOfWellness']) && !empty($postData['touchOfWellness'])) ? $postData['touchOfWellness'] : "No";
            if($touchOfWellness == 1) { $touchOfWellness = "Yes"; }

            // Swap out the placeholders
            $searchArray = array('[Company Name]','[Contact Name]','[Email]','[Web address]','[Wellness Immersion]','[Luxury Wellness]','[Thermal Mineral Springs]','[Eco Sustainable]','[Touch of Wellness]');
            $replaceArray = array($companyName,$contactName,$email,$webAddress,$wellnessImmersion,$wellnessLuxury,$thermalMineralSprings,$ecoSustainable,$touchOfWellness);

            // Send ITL and the property their copies of the email
            $intEmailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'LYWP-INTERNAL'));
            if(isset($intEmailTemplate) && !empty($intEmailTemplate)) {

                $intEmailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $intEmailTemplate['email_template']);

                // Send the email
                foreach($intRecipients as $recipient) {
                    $this->send_email($recipient, $intEmailTemplate['email_subject'], $intEmailTemplate['email_template'], $intEmailTemplate['email_from']);
                }
            }

            // Send the client their email
            $extEmailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'LYWP-CLIENT'));
            if(isset($extEmailTemplate) && !empty($extEmailTemplate)) {
                // Send the email
                $this->send_email($extRecipient, $extEmailTemplate['email_subject'], $extEmailTemplate['email_template'], $extEmailTemplate['email_from'],getcwd(). '/assets/files/InThisLifeWellnessTravel-NewPropertyApplication-Oct19.pdf');
            }

            // Set success message for user
            $this->data['successMessage'] = "<div class=\"alert alert-success\"><h4>Thank you for submitting your details.</h4><p>One of our staff will attend to your enquiry and an application will be sent to you shorty.</p><p>Kind regards</p><p>The Team @ In This Life Wellness Travel</p></div>";
        }

        $this->template->write('title', "List Your Wellness Property" . " - " . COMPANY_NAME);
        $this->template->write('meta_description', 'List Your Wellness Property' . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_keywords', 'List Your Wellness Property' . " - " . COMPANY_TAGLINE);
        $this->template->write_view('content', 'front/home/list_your_wellness_property', $this->data);
        $this->template->render();
    }

    public function wellness_travel_agent_training_program() {

        $this->data['page'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'wellness-travel-agent-training-program', 'p.status' => 1));

//        var_dump($this->data['page']);

        $postData = $this->input->post(NULL, TRUE);

        //var_dump($postData);

        if(isset($postData) && !empty($postData)) {
            // Send off notification emails to internal contacts and client

            // Source who we're sending to internally
            $intRecipients = array('support@inthislifewellnesstravel.com','josh@jcdm.net'); //

            // Prep the data fields
            $companyName = (isset($postData['companyName']) && !empty($postData['companyName'])) ? $postData['companyName'] : "Not supplied";
            $contactName = (isset($postData['contactName']) && !empty($postData['contactName'])) ? $postData['contactName'] : "Not supplied";
            $email = (isset($postData['email']) && !empty($postData['email'])) ? $postData['email'] : "Not supplied";
            $phone = (isset($postData['workPhone']) && !empty($postData['workPhone'])) ? $postData['pre_phone'] . " " . $postData['workPhone'] : "Not supplied";
            $companyRelationship = (isset($postData['companyRelationship']) && !empty($postData['companyRelationship'])) ? $postData['companyRelationship'] : "Not supplied";
            $country = (isset($postData['countryCode']) && !empty($postData['countryCode'])) ? $this->data['countryList'][$postData['countryCode']] : "Not supplied";
            $state = (isset($postData['state_id']) && !empty($postData['state_id'])) ? $this->get_state_base_id($postData['state_id']) : "Not supplied";
            $yearsAsAgent = (isset($postData['yearsAsAgent']) && !empty($postData['yearsAsAgent'])) ? $postData['yearsAsAgent'] : "Not supplied";
            $whyTakeTraining = (isset($postData['whyTakeTraining']) && !empty($postData['whyTakeTraining'])) ? nl2br(trim($postData['whyTakeTraining'])) : "Not supplied";
            $numberTrainees = (isset($postData['numberTrainees']) && !empty($postData['numberTrainees'])) ? $postData['numberTrainees'] : "Not supplied";
            $colleagueName = (isset($postData['colleagueName']) && !empty($postData['colleagueName'])) ? $postData['colleagueName'] : "Not supplied";
            $colleagueTitle = (isset($postData['colleagueTitle']) && !empty($postData['colleagueTitle'])) ? $postData['colleagueTitle'] : "Not supplied";
            $colleagueEmail = (isset($postData['colleagueEmail']) && !empty($postData['colleagueEmail'])) ? $postData['colleagueEmail'] : "Not supplied";

            // Process tick boxes into yes/no values
            $checkBoxes = array('trainingFaceToFace','trainingWebinar','trainingSelfPacedLearning');
            foreach($checkBoxes as $checkBox) {
                $$checkBox = (isset($postData[$checkBox]) && !empty($postData[$checkBox])) ? $postData[$checkBox] : "No";
                if($$checkBox == 1) { $$checkBox = "Yes"; }
            }

            // Swap out the placeholders
            $searchArray = array('[Travel Company Name]','[Contact Name]','[Email]','[Phone number]','[Relationship to company]','[Years as agent]','[Country]','[State]','[Why take training]','[Number of trainees]','[Colleague Name]','[Colleague Title]','[Colleague Email]');
            $replaceArray = array($companyName,$contactName,$email,$phone,$companyRelationship,$yearsAsAgent,$country,$state,$whyTakeTraining,$numberTrainees,$colleagueName,$colleagueTitle,$colleagueEmail);

            // Send ITL their email
            $intEmailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'WTATP-INTERNAL'));
            if(isset($intEmailTemplate) && !empty($intEmailTemplate)) {

                $intEmailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $intEmailTemplate['email_template']);

                // Send the email
                foreach($intRecipients as $recipient) {
                    $this->send_email($recipient, $intEmailTemplate['email_subject'], $intEmailTemplate['email_template'], $intEmailTemplate['email_from']);
                }
            }

            // Send client their email
            if($email != "Not supplied") {
                $extEmailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'WTATP-CLIENT'));
                if(isset($extEmailTemplate) && !empty($extEmailTemplate)) {

                    $extEmailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $extEmailTemplate['email_template']);

                    // Send the email
                    $this->send_email($email, $extEmailTemplate['email_subject'], $extEmailTemplate['email_template'], $extEmailTemplate['email_from']);
                }
            }

            // Set success message for user
            $this->data['successMessage'] = "<div class=\"alert alert-success\"><h4>Thank you for your request for further information on In This Life Wellness Travel’s Wellness Travel Agent Training Program</h4><p>A member of our team will attend to your request and you will receive further correspondence shortly.</p><p>The Team @ In This Life Wellness Travel</p>
</div>";
        }

        $this->data['countryList'] = $this->get_country_list_iso2();

        $this->template->write('title', "Wellness Travel Agent Training Program" . " - " . COMPANY_NAME);
        $this->template->write('meta_description', 'Wellness Travel Agent Training Program' . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_keywords', 'Wellness Travel Agent Training Program' . " - " . COMPANY_TAGLINE);
        $this->template->write_view('content', 'front/home/wellness_travel_agent_training_program', $this->data);
        $this->template->render();
    }

    public function add_wellness_programs_and_packages() {

        $this->data['page'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'add-wellness-programs-packages', 'p.status' => 1));

        $postData = $this->input->post(NULL, TRUE);

//        var_dump($postData);

        if(isset($postData) && !empty($postData)) {
            // Send off notification emails to internal contacts and client

            // Source who we're sending to internally
            $intRecipients = array('support@inthislifewellnesstravel.com','josh@jcdm.net'); //

            // Prep the data fields
            $fieldList = array('propertyName','propertyLink','propertyName','propertyWebsite','propertyAddress','phoneNumber','emailReservations','emailMarketing','yourName','yourPosition','pack1Name','pack1Details','pack1Pricing','pack2Name','pack2Details','pack2Pricing','pack3Name','pack3Details','pack3Pricing','pack4Name','pack4Details','pack4Pricing','pack5Name','pack5Details','pack5Pricing','pack6Name','pack6Details','pack6Pricing','agree_terms_and_conditions','admin_fee_agree');
            foreach($fieldList as $fieldName) {
                $$fieldName = (isset($postData[$fieldName]) && !empty($postData[$fieldName])) ? nl2br($postData[$fieldName]) : "Not supplied";
            }

            // Process tick boxes into yes/no values
            $checkBoxes = array('agree_terms_and_conditions','admin_fee_agree');
            foreach($checkBoxes as $checkBox) {
                $$checkBox = (isset($postData[$checkBox]) && !empty($postData[$checkBox])) ? $postData[$checkBox] : "No";
                if($$checkBox == 1) { $$checkBox = "Yes"; }
            }

            // Swap out the placeholders
            $searchArray = array("[propertyName]","[propertyWebsite]","[propertyAddress]","[phoneNumber]","[emailReservations]","[emailMarketing]","[yourName]","[yourPosition]","[pack1Name]","[pack1Details]","[pack1Pricing]","[pack2Name]","[pack2Details]","[pack2Pricing]","[pack3Name]","[pack3Details]","[pack3Pricing]","[pack4Name]","[pack4Details]","[pack4Pricing]","[pack5Name]","[pack5Details]","[pack5Pricing]","[pack6Name]","[pack6Details]","[pack6Pricing]","[agree_terms_and_conditions]","[admin_fee_agree]");
            $replaceArray = array($propertyName,$propertyWebsite,$propertyAddress,$phoneNumber,$emailReservations,$emailMarketing,$yourName,$yourPosition,$pack1Name,$pack1Details,$pack1Pricing,$pack2Name,$pack2Details,$pack2Pricing,$pack3Name,$pack3Details,$pack3Pricing,$pack4Name,$pack4Details,$pack4Pricing,$pack5Name,$pack5Details,$pack5Pricing,$pack6Name,$pack6Details,$pack6Pricing,$agree_terms_and_conditions,$admin_fee_agree);

            // Send ITL their email
            $intEmailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'WPPU-INTERNAL'));
            if(isset($intEmailTemplate) && !empty($intEmailTemplate)) {

                $intEmailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $intEmailTemplate['email_template']);

                // Send the email
                foreach($intRecipients as $recipient) {
                    $this->send_email($recipient, $intEmailTemplate['email_subject'], $intEmailTemplate['email_template'], $intEmailTemplate['email_from']);
                }
            }

            // Send client their email
            if($emailMarketing != "Not supplied") {
                $extEmailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'WPPU-CLIENT'));
                if(isset($extEmailTemplate) && !empty($extEmailTemplate)) {

                    $extEmailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $extEmailTemplate['email_template']);

                    // Send the email
                    $this->send_email($emailMarketing, $extEmailTemplate['email_subject'], $extEmailTemplate['email_template'], $extEmailTemplate['email_from']);
                }
            }

            // Set success message for user
            $this->data['successMessage'] = "<div class=\"alert alert-success\"><h4>Thank you for submitting your application to add your Wellness Programs & Packages</h4><p>Your application will be assessed by our team, and we will notify you via email when your application has been approved.</p><p><a href=\"https://www.inthislifewellnesstravel.com/update-property-listing-details\">Please continue on to the 'Update Property Listing Details' page by clicking here to complete more information for your property.</a></p></div>";
        }

        $this->template->write('title', "Add Wellness Programs and Packages" . " - " . COMPANY_NAME);
        $this->template->write('meta_description', 'Our site now offers travellers the opportunity for clients to pre-book your Wellness Programs and Packages in advance, prior to travel. Add your Wellness Programs and Packages onto our site to increase your revenue and improve your guest Wellness experience.' . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_keywords', 'Add Wellness Programs and Packages' . " - " . COMPANY_TAGLINE);
        $this->template->write_view('content', 'front/home/add_wellness_programs_and_packages', $this->data);
        $this->template->render();
    }

    public function update_property_listing_details() {

        $this->data['page'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'update-property-listing-details', 'p.status' => 1));

        $postData = $this->input->post(NULL, TRUE);

//        var_dump($postData);

        if(isset($postData) && !empty($postData)) {
            // Send off notification emails to internal contacts and client

            // Source who we're sending to internally
            $intRecipients = array('support@inthislifewellnesstravel.com','josh@jcdm.net'); //

            // Prep the data fields
            $fieldList = array('propertyName','propertyWebsite','propertyAddress','phoneNumber','emailReservations','emailMarketing','yourName','yourPosition','wellnessStyle','wsWellnessImmersion','wsLuxury','wsSprings','wsEcoSustainable','wsTouch','wtaChange','cbMassageSpaBodyTreatments','cbAyurveda','cbSauna','cbThalassaTherapy','cbHydrotherapy','cbYoga','cbMeditation','cbSpiritual','cbPilates','cbDetox','cbWeightManagement','cbMediClinic','cbAntiAgeing','cbHealthConsultations','cbVegetarianOptions','cbVeganOptions','cbOrganicFood','cbOrganicGardenOnsite','cbHealthFood','cbSurfing','cbDiving','cbStandUpPaddle','cbHikingBiking','cbAdventureAdrenaline','cbFitness','cbSki','cbGolf','taOther','featuresChange','wfWellnessFeatures','wfTreatmentsActivities','wfDining','wfEcoSustainable','agree_terms_and_conditions');
            foreach($fieldList as $fieldName) {
                $$fieldName = (isset($postData[$fieldName]) && !empty($postData[$fieldName])) ? nl2br($postData[$fieldName]) : "Not supplied";
            }

            // Process tick boxes into yes/no values
            $checkBoxes = array('wsWellnessImmersion','wsLuxury','wsSprings','wsEcoSustainable','wsTouch','wtaChange','cbMassageSpaBodyTreatments','cbAyurveda','cbSauna','cbThalassaTherapy','cbHydrotherapy','cbYoga','cbMeditation','cbSpiritual','cbPilates','cbDetox','cbWeightManagement','cbMediClinic','cbAntiAgeing','cbHealthConsultations','cbVegetarianOptions','cbVeganOptions','cbOrganicFood','cbOrganicGardenOnsite','cbHealthFood','cbSurfing','cbDiving','cbStandUpPaddle','cbHikingBiking','cbAdventureAdrenaline','cbFitness','cbSki','cbGolf','agree_terms_and_conditions');
            foreach($checkBoxes as $checkBox) {
                $$checkBox = (isset($postData[$checkBox]) && !empty($postData[$checkBox])) ? $postData[$checkBox] : "No";
                if($$checkBox == 1) { $$checkBox = "Yes"; }
            }

            // Swap out the placeholders
            $searchArray = array("[propertyName]","[propertyWebsite]","[propertyAddress]","[phoneNumber]","[emailReservations]","[emailMarketing]","[yourName]","[yourPosition]","[wellnessStyle]","[wsWellnessImmersion]","[wsLuxury]","[wsSprings]","[wsEcoSustainable]","[wsTouch]","[wtaChange]","[cbMassageSpaBodyTreatments]","[cbAyurveda]","[cbSauna]","[cbThalassaTherapy]","[cbHydrotherapy]","[cbYoga]","[cbMeditation]","[cbSpiritual]","[cbPilates]","[cbDetox]","[cbWeightManagement]","[cbMediClinic]","[cbAntiAgeing]","[cbHealthConsultations]","[cbVegetarianOptions]","[cbVeganOptions]","[cbOrganicFood]","[cbOrganicGardenOnsite]","[cbHealthFood]","[cbSurfing]","[cbDiving]","[cbStandUpPaddle]","[cbHikingBiking]","[cbAdventureAdrenaline]","[cbFitness]","[cbSki]","[cbGolf]","[taOther]","[featuresChange]","[wfWellnessFeatures]","[wfTreatmentsActivities]","[wfDining]","[wfEcoSustainable]","[agree_terms_and_conditions]");
            $replaceArray = array($propertyName,$propertyWebsite,$propertyAddress,$phoneNumber,$emailReservations,$emailMarketing,$yourName,$yourPosition,$wellnessStyle,$wsWellnessImmersion,$wsLuxury,$wsSprings,$wsEcoSustainable,$wsTouch,$wtaChange,$cbMassageSpaBodyTreatments,$cbAyurveda,$cbSauna,$cbThalassaTherapy,$cbHydrotherapy,$cbYoga,$cbMeditation,$cbSpiritual,$cbPilates,$cbDetox,$cbWeightManagement,$cbMediClinic,$cbAntiAgeing,$cbHealthConsultations,$cbVegetarianOptions,$cbVeganOptions,$cbOrganicFood,$cbOrganicGardenOnsite,$cbHealthFood,$cbSurfing,$cbDiving,$cbStandUpPaddle,$cbHikingBiking,$cbAdventureAdrenaline,$cbFitness,$cbSki,$cbGolf,$taOther,$featuresChange,$wfWellnessFeatures,$wfTreatmentsActivities,$wfDining,$wfEcoSustainable,$agree_terms_and_conditions);

            // Send ITL their email
            $intEmailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'UPLD-INTERNAL'));
            if(isset($intEmailTemplate) && !empty($intEmailTemplate)) {

                $intEmailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $intEmailTemplate['email_template']);

                // Send the email
                foreach($intRecipients as $recipient) {
                    $this->send_email($recipient, $intEmailTemplate['email_subject'], $intEmailTemplate['email_template'], $intEmailTemplate['email_from']);
                }
            }

            // Send client their email
            if($emailMarketing != "Not supplied") {
                $extEmailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'UPLD-CLIENT'));
                if(isset($extEmailTemplate) && !empty($extEmailTemplate)) {

                    $extEmailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $extEmailTemplate['email_template']);

                    // Send the email
                    $this->send_email($emailMarketing, $extEmailTemplate['email_subject'], $extEmailTemplate['email_template'], $extEmailTemplate['email_from']);
                }
            }

            // Set success message for user
            $this->data['successMessage'] = "<div class=\"alert alert-success\"><h4>Thank you for updating your wellness property information</h4><p>Our team will notify you via when your updates have been made.</p></div>";
        }

        $this->template->write('title', "Update Property Listing Details" . " - " . COMPANY_NAME);
        $this->template->write('meta_description', 'It is extremely important that In This Life Wellness Travel displays information to your potential guests accurately. Please complete this form to request changes to your listing' . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_keywords', 'Update Property Listing Details' . " - " . COMPANY_TAGLINE);
        $this->template->write_view('content', 'front/home/update_property_listing_details', $this->data);
        $this->template->render();
    }

    // Added by JCDM Mar 2020
    public function write_form_data_to_file($formName = "",$fileName = "",$formData = "") {
        // Eject if something isn't provided correctly
        if($formName == "") {
            return "B0rk formName";
        }
        if($fileName == "") {
            return "B0rk fileName";
        }
        if($formData == "") {
            return "B0rk formData";
        }

        // Sanitise form name and file name
        $formName = preg_replace('/[^A-Za-z0-9]/','',$formName);
        $fileName = preg_replace('/[^A-Za-z0-9]/', '', $fileName) . ".json";

        // Read out existing JSON data and combine it if it already exists
        if(file_exists('/home/inthislifewell/formData/' . $formName . "-" . $fileName)) {
            $fileContent = file_get_contents('/home/inthislifewell/formData/' . $formName . "-" . $fileName);
            $existingData = json_decode($fileContent,1);
            foreach($formData as $key=>$data) {
                $existingData[$key] = $data;
            }
            $formData = $existingData;
        }

        // Prep form data
        $dataToWrite = json_encode($formData);
        $result = file_put_contents('/home/inthislifewell/formData/' . $formName . "-" . $fileName,$dataToWrite);
        return print_r($result,1);
    }

    // Added by JCDM Mar 2020
    public function read_form_data_from_file($formName = "",$fileName = "") {
        // Eject if something isn't provided correctly
        if($formName == "") {
            return "B0rk formName";
        }
        if($fileName == "") {
            return "B0rk fileName";
        }

        // Sanitise form name and file name
        $formName = preg_replace('/[^A-Za-z0-9]/','',$formName);
        $fileName = preg_replace('/[^A-Za-z0-9]/', '', $fileName) . ".json";

        // Read out existing JSON data and combine it if it already exists
        if(file_exists('/home/inthislifewell/formData/' . $formName . "-" . $fileName)) {
            $fileContent = file_get_contents('/home/inthislifewell/formData/' . $formName . "-" . $fileName);
            $existingData = json_decode($fileContent,1);

//            Change 'nodata' to blank
            foreach($existingData as $key => $data) {
                if($data == "[nodata]") {
                    $existingData[$key] = "";
                }
            }

            return $existingData;
        }
        return;

    }

    public function property_listing_application_1() {

        // Set the cookie for these forms if it doesn't exist
        if(!isset($_COOKIE['plauid'])) {
            $uid = hash("sha256",time());
            setcookie('plauid', $uid);
            $_COOKIE['plauid'] = $uid;
        }

        // Get form data into a variable
        $formPostData = $this->input->post(NULL, TRUE);

        // If form data, process it
        if(isset($formPostData) && !empty($formPostData)) {

            // List of all fields
            $allFields = array("uid","propertyName","propertyWebsite","propertyAddress","phoneNumber","emailReservations","emailMarketing","yourName","yourPosition","channelExpedia","includesEAN","channelBooking","channelWebBeds","channelOther","hhITLDirect","hhReferred","hhReferree","hhSearchEngineGoogle","hhSearchEngineBing","hhSearchEngineYahoo","hhSearchEngineYouTube","hhSocialFacebook","hhSocialLinkedIn","hhSocialInstagram","hhOther","gdBookingsCurrently","gdSuccessfulStrategies","gdFromCountries","gdAge","gdOtherDemographics","gdWhoTarget","gdOtherDemographicInfo","wsLuxury","wsSprings","wsEcoSustainable","wsTouch");

            $completeData = array();
            // Populate set of all fields with placeholder
            foreach($allFields as $field) {
                $completeData[$field] = "[nodata]";
            }
            // Overwrite placeholder from formdata
            foreach($formPostData as $formField => $formData) {
                if(trim($formData) != "") {
                    $completeData[$formField] = $formData;
                }
            }

            // Check if anything compulsory is missing
            $compulsory = array("propertyName","propertyWebsite","propertyAddress","phoneNumber","emailReservations","emailMarketing","yourName","yourPosition");

            $failedCompulsory = 0;
            $failedOn = "";
            foreach($compulsory as $compulsoryField) {
                if($completeData[$compulsoryField] == "[nodata]") {
                    $failedCompulsory = 1;
                    $failedOn .= $compulsoryField . " | ";
                }
            }

            $this->data['postDataString'] = print_r($completeData,1) . $failedOn;

            // If compulsory missing, push back with populated values
            if($failedCompulsory == 1) {
                $this->data['postData'] = $formPostData;
            }

            // If compulsory present, save and proceed to page 2
            if($failedCompulsory == 0) {
                $this->data['fileWriteResult'] = $this->write_form_data_to_file('Property-Listing-Application', $formPostData['uid'], $completeData);
                $this->load->helper('url');
                redirect('/property-listing-application-2','refresh');
            }

        }

        // If no form data, show form
        $this->data['page'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'property-listing-application', 'p.status' => 1));

        $fileData = $this->read_form_data_from_file('Property-Listing-Application', $_COOKIE['plauid']);
//        pr($fileData);
        if(is_array($fileData)) {
            $inFromFile = array();
            foreach($fileData as $key => $data) {
                $inFromFile[$key] = $data;
            }
            $this->data['postData'] = $inFromFile;
        }

        //
        $this->template->write('title', "Property Listing Application - Page 1 of 4" . " - " . COMPANY_NAME);
        $this->template->write('meta_description', 'Please complete this form to apply for listing on In This Life Wellness Travel' . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_keywords', 'Please complete this form to apply for listing on In This Life Wellness Travel' . " - " . COMPANY_TAGLINE);
        $this->template->write_view('content', 'front/home/property_listing_application_1', $this->data);
        $this->template->render();
    }

    public function property_listing_application_2() {

        // Get form data into a variable
        $formPostData = $this->input->post(NULL, TRUE);

        // If form data, process it
        if(isset($formPostData) && !empty($formPostData)) {

            // List of all fields
            $allFields = array("uid","cbMassageSpaBodyTreatments","cbAyurveda","cbSauna","cbThalassaTherapy","cbHydrotherapy","cbYoga","cbMeditatio","cbSpiritual","cbPilates","cbDetox","cbWeightManagement","cbMediClinic","cbAntiAgeing","cbHealthConsultations","cbVegetarianOptions","cbVeganOptions","cbOrganicFood","cbOrganicGardenOnsite","cbHealthFood","cbSurfing","cbDiving","cbStandUpPaddle","cbHikingBiking","cbAdventureAdrenaline","cbFitness","cbSki","cbGolf","taOther","wfWellnessFeatures","wfTreatmentsActivities","wfDining","wfEcoSustainable");

            $completeData = array();
            // Populate set of all fields with placeholder
            foreach($allFields as $field) {
                $completeData[$field] = "[nodata]";
            }
            // Overwrite placeholder from formdata
            foreach($formPostData as $formField => $formData) {
                if(trim($formData) != "") {
                    $completeData[$formField] = $formData;
                }
            }

            // Save and proceed to page 3 or back to page 1
            $this->data['fileWriteResult'] = $this->write_form_data_to_file('Property-Listing-Application', $formPostData['uid'], $completeData);
            $this->load->helper('url');
            if($formPostData['direction'] == "next") {
                redirect('/property-listing-application-3', 'refresh');
            } else {
                redirect('/property-listing-application', 'refresh');
            }
        }

        // If no form data, show form
        $this->data['page'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'property-listing-application', 'p.status' => 1));

        $fileData = $this->read_form_data_from_file('Property-Listing-Application', $_COOKIE['plauid']);
//        pr($fileData);
        if(is_array($fileData)) {
            $inFromFile = array();
            foreach($fileData as $key => $data) {
                $inFromFile[$key] = $data;
            }
            $this->data['postData'] = $inFromFile;
        }

        // If no form data, show form
        $this->data['page'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'property-listing-application-2', 'p.status' => 1));

        $this->template->write('title', "Property Listing Application - Page 2 of 4" . " - " . COMPANY_NAME);
        $this->template->write('meta_description', 'Please complete this form to apply for listing on In This Life Wellness Travel' . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_keywords', 'Please complete this form to apply for listing on In This Life Wellness Travel' . " - " . COMPANY_TAGLINE);
        $this->template->write_view('content', 'front/home/property_listing_application_2', $this->data);
        $this->template->render();
    }

    public function property_listing_application_3() {

        // Get form data into a variable
        $formPostData = $this->input->post(NULL, TRUE);

        // If form data, process it
        if(isset($formPostData) && !empty($formPostData)) {

            // List of all fields
            $allFields = array("uid","pack1Name","pack1Details","pack1Pricing","pack2Name","pack2Details","pack2Pricing","pack3Name","pack3Details","pack3Pricing","pack4Name","pack4Details","pack4Pricing","pack5Name","pack5Details","pack5Pricing","pack6Name","pack6Details","pack6Pricing");

            $completeData = array();
            // Populate set of all fields with placeholder
            foreach($allFields as $field) {
                $completeData[$field] = "[nodata]";
            }
            // Overwrite placeholder from formdata
            foreach($formPostData as $formField => $formData) {
                if(trim($formData) != "") {
                    $completeData[$formField] = $formData;
                }
            }

            // Save and proceed to page 4 or back to page 2
            $this->data['fileWriteResult'] = $this->write_form_data_to_file('Property-Listing-Application', $formPostData['uid'], $completeData);
            $this->load->helper('url');
            if($formPostData['direction'] == "next") {
                redirect('/property-listing-application-4', 'refresh');
            } else {
                redirect('/property-listing-application-2', 'refresh');
            }
        }

        // If no form data, show form
        $this->data['page'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'property-listing-application', 'p.status' => 1));

        $fileData = $this->read_form_data_from_file('Property-Listing-Application', $_COOKIE['plauid']);
//        pr($fileData);
        if(is_array($fileData)) {
            $inFromFile = array();
            foreach($fileData as $key => $data) {
                $inFromFile[$key] = $data;
            }
            $this->data['postData'] = $inFromFile;
        }

        $this->data['page'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'property-listing-application-3', 'p.status' => 1));

        $this->template->write('title', "Property Listing Application - Page 3 of 4" . " - " . COMPANY_NAME);
        $this->template->write('meta_description', 'Please complete this form to apply for listing on In This Life Wellness Travel' . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_keywords', 'Please complete this form to apply for listing on In This Life Wellness Travel' . " - " . COMPANY_TAGLINE);
        $this->template->write_view('content', 'front/home/property_listing_application_3', $this->data);
        $this->template->render();
    }

    public function property_listing_application_4() {

        $this->data['page'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'property-listing-application-4', 'p.status' => 1));

        $formPostData = $this->input->post(NULL, TRUE);

//        pr($postData);

        if(isset($formPostData) && !empty($formPostData)) {
            // Send off notification emails to internal contacts and client

            // Source who we're sending to internally
            $intRecipients = array('support@inthislifewellnesstravel.com','josh@jcdm.net'); //

            // Prep the data fields
            $fieldList = array('propertyName','propertyWebsite','propertyAddress','phoneNumber','emailReservations','emailMarketing','yourName','yourPosition','wellnessStyle','wsWellnessImmersion','wsLuxury','wsSprings','wsEcoSustainable','wsTouch','wtaChange','cbMassageSpaBodyTreatments','cbAyurveda','cbSauna','cbThalassaTherapy','cbHydrotherapy','cbYoga','cbMeditation','cbSpiritual','cbPilates','cbDetox','cbWeightManagement','cbMediClinic','cbAntiAgeing','cbHealthConsultations','cbVegetarianOptions','cbVeganOptions','cbOrganicFood','cbOrganicGardenOnsite','cbHealthFood','cbSurfing','cbDiving','cbStandUpPaddle','cbHikingBiking','cbAdventureAdrenaline','cbFitness','cbSki','cbGolf','taOther','featuresChange','wfWellnessFeatures','wfTreatmentsActivities','wfDining','wfEcoSustainable','agree_terms_and_conditions');
            foreach($fieldList as $fieldName) {
                $$fieldName = (isset($formPostData[$fieldName]) && !empty($formPostData[$fieldName])) ? nl2br($formPostData[$fieldName]) : "Not supplied";
            }

            // Process tick boxes into yes/no values
            $checkBoxes = array('wsWellnessImmersion','wsLuxury','wsSprings','wsEcoSustainable','wsTouch','wtaChange','cbMassageSpaBodyTreatments','cbAyurveda','cbSauna','cbThalassaTherapy','cbHydrotherapy','cbYoga','cbMeditation','cbSpiritual','cbPilates','cbDetox','cbWeightManagement','cbMediClinic','cbAntiAgeing','cbHealthConsultations','cbVegetarianOptions','cbVeganOptions','cbOrganicFood','cbOrganicGardenOnsite','cbHealthFood','cbSurfing','cbDiving','cbStandUpPaddle','cbHikingBiking','cbAdventureAdrenaline','cbFitness','cbSki','cbGolf','agree_terms_and_conditions');
            foreach($checkBoxes as $checkBox) {
                $$checkBox = (isset($formPostData[$checkBox]) && !empty($formPostData[$checkBox])) ? $formPostData[$checkBox] : "No";
                if($$checkBox == 1) { $$checkBox = "Yes"; }
            }

            // Swap out the placeholders
            $searchArray = array("[propertyName]","[propertyWebsite]","[propertyAddress]","[phoneNumber]","[emailReservations]","[emailMarketing]","[yourName]","[yourPosition]","[wellnessStyle]","[wsWellnessImmersion]","[wsLuxury]","[wsSprings]","[wsEcoSustainable]","[wsTouch]","[wtaChange]","[cbMassageSpaBodyTreatments]","[cbAyurveda]","[cbSauna]","[cbThalassaTherapy]","[cbHydrotherapy]","[cbYoga]","[cbMeditation]","[cbSpiritual]","[cbPilates]","[cbDetox]","[cbWeightManagement]","[cbMediClinic]","[cbAntiAgeing]","[cbHealthConsultations]","[cbVegetarianOptions]","[cbVeganOptions]","[cbOrganicFood]","[cbOrganicGardenOnsite]","[cbHealthFood]","[cbSurfing]","[cbDiving]","[cbStandUpPaddle]","[cbHikingBiking]","[cbAdventureAdrenaline]","[cbFitness]","[cbSki]","[cbGolf]","[taOther]","[featuresChange]","[wfWellnessFeatures]","[wfTreatmentsActivities]","[wfDining]","[wfEcoSustainable]","[agree_terms_and_conditions]");
            $replaceArray = array($propertyName,$propertyWebsite,$propertyAddress,$phoneNumber,$emailReservations,$emailMarketing,$yourName,$yourPosition,$wellnessStyle,$wsWellnessImmersion,$wsLuxury,$wsSprings,$wsEcoSustainable,$wsTouch,$wtaChange,$cbMassageSpaBodyTreatments,$cbAyurveda,$cbSauna,$cbThalassaTherapy,$cbHydrotherapy,$cbYoga,$cbMeditation,$cbSpiritual,$cbPilates,$cbDetox,$cbWeightManagement,$cbMediClinic,$cbAntiAgeing,$cbHealthConsultations,$cbVegetarianOptions,$cbVeganOptions,$cbOrganicFood,$cbOrganicGardenOnsite,$cbHealthFood,$cbSurfing,$cbDiving,$cbStandUpPaddle,$cbHikingBiking,$cbAdventureAdrenaline,$cbFitness,$cbSki,$cbGolf,$taOther,$featuresChange,$wfWellnessFeatures,$wfTreatmentsActivities,$wfDining,$wfEcoSustainable,$agree_terms_and_conditions);

            // Send ITL their email
            $intEmailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'UPLD-INTERNAL'));
            if(isset($intEmailTemplate) && !empty($intEmailTemplate)) {

                $intEmailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $intEmailTemplate['email_template']);

                // Send the email
                foreach($intRecipients as $recipient) {
                    $this->send_email($recipient, $intEmailTemplate['email_subject'], $intEmailTemplate['email_template'], $intEmailTemplate['email_from']);
                }
            }

            // Send client their email
            if($emailMarketing != "Not supplied") {
                $extEmailTemplate = $this->app_model->find('email_templates e', 'first', '', array('e.email_name' => 'UPLD-CLIENT'));
                if(isset($extEmailTemplate) && !empty($extEmailTemplate)) {

                    $extEmailTemplate['email_template'] = str_replace($searchArray, $replaceArray, $extEmailTemplate['email_template']);

                    // Send the email
                    $this->send_email($emailMarketing, $extEmailTemplate['email_subject'], $extEmailTemplate['email_template'], $extEmailTemplate['email_from']);
                }
            }

            // Set success message for user
            $this->data['successMessage'] = "<div class=\"alert alert-success\"><h4>Thank you for updating your wellness property information</h4><p>Our team will notify you via when your updates have been made.</p></div>";

            // Finish or go back to page 3
            if($formPostData['direction'] == "next") {
//                pr($formPostData);
                $this->template->write_view('content', 'front/home/property_listing_application_4', $this->data);
                $this->template->render();
            } else {
                redirect('/property-listing-application-3', 'refresh');
            }

        } else {

            // If no form data, show form
            $this->data['page'] = $this->app_model->find('pages p', 'first', '', array('p.slug' => 'property-listing-application', 'p.status' => 1));

            $fileData = $this->read_form_data_from_file('Property-Listing-Application', $_COOKIE['plauid']);
//        pr($fileData);
            if (is_array($fileData)) {
                $inFromFile = array();
                foreach ($fileData as $key => $data) {
                    $inFromFile[$key] = $data;
                }
                $this->data['postData'] = $inFromFile;
            }

            $this->template->write('title', "Property Listing Application - Page 4 of 4" . " - " . COMPANY_NAME);
            $this->template->write('meta_description', 'Please complete this form to apply for listing on In This Life Wellness Travel' . " - " . COMPANY_TAGLINE);
            $this->template->write('meta_keywords', 'Please complete this form to apply for listing on In This Life Wellness Travel' . " - " . COMPANY_TAGLINE);

            $this->template->write_view('content', 'front/home/property_listing_application_4', $this->data);
            $this->template->render();
        }
    }

    public function success($booking = null)
    {
        //pr($booking) ;
        if(!empty($booking))
        {
            if($this->session->has_userdata('postData'))
            {
                $this->session->unset_userdata('postData');
            }


            $this->data['booking'] = $booking;
            $this->template->write('title', "Success" . " - " . COMPANY_NAME);
            $this->template->write('meta_description', 'Success' . " - " . COMPANY_TAGLINE);
            $this->template->write('meta_keywords', 'Success' . " - " . COMPANY_TAGLINE);
            $this->template->write_view('content', 'front/home/success', $this->data);
            $this->template->render();
        }
        else
        {
            redirect('/');
        }
    }

    public function change_room_checkout()
    {
        if(!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $post = $this->input->post();
        $this->data['searchData'] = base64_decode(json_decode($post['searchData'], true));
        $this->data['expedia'] = base64_decode(json_decode($post['searchData'], true));
        $this->data['room_specfic_checkout'] = base64_decode(json_decode($post['room_specfic_checkout'], true));
//        pr($post);
    }

    public function cancel_booking($param1, $param2, $param3, $param4 = '')
    {
        $userData = $this->session->userdata('customer');
        $response = $this->cancel_reservation($param2, $param1, $userData['email']);
        $responseData = json_decode($response, true);
        //cancellationNumber
        if(isset($responseData['HotelRoomCancellationResponse']) && !empty($responseData['HotelRoomCancellationResponse']['cancellationNumber']))
        {
            $status = array();
            $status['cancellationNumber'] = isset($responseData['HotelRoomCancellationResponse']['cancellationNumber']) ? $responseData['HotelRoomCancellationResponse']['cancellationNumber'] : '';
            $this->app_model->save('order_room_specific_status', $status, array('confirmationNumbers' => $param1, 'order_id' => $param3));
            $cancellationNumber = $status['cancellationNumber'];
            $this->data['status'] = $this->app_model->find('order_room_specific_status s', 'list', array('s.confirmationNumbers', 's.cancellationNumber'), array('s.cancellationNumber' => '', 'order_id' => $param3));


            $status = array();
            if(empty($this->data['status']))
            {

                $status['reservationStatusCode'] = 'FC';
            }
            else
            {
                $status['reservationStatusCode'] = 'PC';
            }
            $status['id'] = $param3;

            $this->app_model->save('orders', $status);
            $this->cancel_email($cancellationNumber, $param1, $param3);
            $this->session->set_flashdata('success', roomBooking);
            if($param4 == '1234')
                redirect(base_url() . 'booking_details/' . $param3);
            else
                redirect(base_url() . 'my_booking');
        }
        else
        {
            $this->session->set_flashdata('error', $responseData['HotelRoomCancellationResponse']['EanWsError']['presentationMessage']);
            redirect(base_url() . 'my_booking');
        }
    }

    public function get_all_activities()
    {
        if(!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }

        $post = $this->input->post();
        if(isset($post) && !empty($post))
        {
            $activities = $this->get_hotels_style($post['hotel_id']);
            $this->data['activities'] = $activities;
            //pr($this->data);die;
            $html = $this->load->view('front/home/common/wellness_profile_ajax', $this->data, true);
            $data = array('html' => $html);
            echo json_encode($data);
        }
        die;
    }

    public function my_wishlist($id = null)
    {
        $userData = $this->session->userdata('customer');
        if(!empty($userData))
        {
            $this->data['userData'] = $userData;
        }
        else
        {
            $this->data['userData'] = "";
        }
        if(isset($userData) && $userData != "")
        {
            $all_wishlist = $this->db->query('SELECT uw.id as listid,uw.item_id,uw.is_item,hs.name,hsd.promoDescription,hs.in_this_life_desc,hs.EANHotelID as EANHotelID,hsd.hotelid as hotelid FROM user_wishlist uw LEFT JOIN hotels_select hs ON hs.EANHotelID = uw.item_id LEFT JOIN hotel_select_deals hsd ON hsd.promoId = uw.item_id LEFT JOIN hotel_select_details hd ON hd.EANHotelID = uw.item_id where ((uw.is_item = "hotels") or (uw.is_item = "deals"))  and (uw.user_id = ' . $userData['id'] . ') and (uw.is_wish = 1) order by uw.id DESC');

            $count = 0;
            $get_all_wishlist = array();
            foreach($all_wishlist->result() as $row)
            {
                $get_all_wishlist[$count] = (array) $row;
                if($row->is_item == "deals")
                {
                    //$apiData = $this->get_live_data_from_hotel_ids($row->hotelid);
                }
                else
                {
                    // $apiData = $this->get_live_data_from_hotel_ids($row->EANHotelID);
                }
                // pr($apiData);
                $count++;
            }
            foreach($get_all_wishlist as $wishlistdata)
            {
                if($wishlistdata['is_item'] == "deals")
                {
                    $wishdata_array[$wishlistdata['item_id']]['item_id'] = $wishlistdata['item_id'];
                    $wishdata_array[$wishlistdata['item_id']]['item_name'] = $wishlistdata['is_item'];
                }
                else
                {
                    $wishdata_array[$wishlistdata['item_id']]['item_id'] = $wishlistdata['item_id'];
                    $wishdata_array[$wishlistdata['item_id']]['item_name'] = $wishlistdata['is_item'];
                }
            }
            // pr( $wishdata_array );
        }
        else
        {
            $current_cookie = get_cookie('wishlist');
            if(isset($current_cookie) && $current_cookie != "")
            {
                $get_current_cookie = get_cookie('wishlist');
                $cookiedata = unserialize($get_current_cookie);
                $wishdata_array = $cookiedata;
            }
        }
        $hotelscontent1 = array();
        if(isset($wishdata_array))
        {
            $hotelscontent = array();
            $get_hotels_ids = $this->get_hotels_ids_from_list($wishdata_array);
            if(!empty($get_hotels_ids))
            {
                $hotelsId = implode(',', $get_hotels_ids);
                $room = isset($post['rooms']) ? $post['rooms'] : '';
                $call_api = $this->call_ean_api('list', selected_currency, $hotelsId, 'en_US', '', '', '', '');
                $decode = json_decode($call_api, true);

                if(isset($decode['HotelListResponse']['HotelList']['HotelSummary']))
                {

                    if($decode['HotelListResponse']['HotelList']['@size'] <= 1)
                    {

                        $hotelscontent1[] = $decode['HotelListResponse']['HotelList']['HotelSummary'];
                    }
                    else
                    {
                        $hotelscontent1 = $decode['HotelListResponse']['HotelList']['HotelSummary'];
                    }
                    //$hotelscontent = $decode['HotelListResponse']['HotelList']['HotelSummary'];
                }
            }
            $get_deals_ids = $this->get_deals_ids_from_list($wishdata_array);
            if(!empty($get_deals_ids))
            {
                $this->db->where_in('hsd.promoId', $get_deals_ids);
                $deals_array = $this->app_model->find('hotel_select_deals hsd', 'list', array('hsd.promoId as deal_id', 'hsd.hotelId'), array('hsd.is_active' => 1));
                if(!empty($deals_array))
                {
                    $hotelsId = implode(',', array_unique($deals_array));
                    $room = isset($post['rooms']) ? $post['rooms'] : '';
                    $call_api = $this->call_ean_api('list', selected_currency, $hotelsId, 'en_US', '', '', '', '');
                    $decode = json_decode($call_api, true);
                    if(isset($decode['HotelListResponse']['HotelList']['HotelSummary']))
                    {
                        if($decode['HotelListResponse']['HotelList']['@size'] <= 1)
                        {

                            $hotelscontent2[] = $decode['HotelListResponse']['HotelList']['HotelSummary'];
                        }
                        else
                        {
                            $hotelscontent2 = $decode['HotelListResponse']['HotelList']['HotelSummary'];
                        }
                    }
                }
                if(!empty($hotelscontent2))
                {
                    if(!empty($hotelscontent1))
                    {
                        $hotelscontent = array_merge($hotelscontent1, $hotelscontent2);
                    }
                    else
                    {
                        $hotelscontent = $hotelscontent2;
                    }
                }
                else
                {
                    $hotelscontent = $hotelscontent1;
                }   //pr($hotelscontent);
                if(!empty($hotelscontent))
                {
                    if(isset($hotelscontent['hotelId']))
                    {
                        $datahotel = $hotelscontent;
                        $hotelscontent = array();
                        $hotelscontent[] = $datahotel;
                    }
                    foreach($hotelscontent as $key => $hotelsdatalist)
                    {
                        //pr($key);
                        $this->db->order_by('images.is_main', 'DESC');
                        $images = $this->app_model->find('hotel_images images', 'all', 'images.*', array('images.EANHotelID' => $hotelsdatalist['hotelId']));
                        $is_final_data[$hotelsdatalist['hotelId']] = $hotelsdatalist;
                        $is_final_data[$hotelsdatalist['hotelId']]['image_path'] = $images;
                        $is_final_data[$hotelsdatalist['hotelId']]['shortDescription'] = $hotelsdatalist['shortDescription'];
                        $is_final_data[$hotelsdatalist['hotelId']]['expediaData'] = $hotelsdatalist;
                    }
                }
                //pr($is_final_data); 
                $this->db->where_in('hsd.promoId', $get_deals_ids);
                $deals_array = $this->app_model->find('hotel_select_deals hsd', 'all', array('hsd.*', 'hsd.promoId as deal_id', 'hsd.promoDescription as deals_name'), array('hsd.is_active' => 1));
            }
            $all_wish_deals_array = array();
            $all_wish_hotels_array = array();
            foreach($wishdata_array as $cookiekey)
            {
                if(isset($cookiekey['item_id']) && isset($cookiekey['item_name']))
                {
                    if($cookiekey['item_name'] == 'deals')
                    {
                        $this->db->order_by('hi.is_main', 'DESC');
                        $deals_array = $this->app_model->find('hotel_select_deals hsd', 'first', array('hsd.*', 'hs.*', 'hd.*', 'hi.*', 'hsd.promoId as deal_id', 'hsd.promoDescription as deals_name'), array('hsd.is_active' => 1, 'hsd.promoId' => $cookiekey['item_id']), array(
                            array('hotels_select hs', 'hs.EANHotelID=hsd.hotelId', 'left'),
                            array('hotel_images hi', 'hi.EANHotelID=hsd.hotelId', 'left'),
                            array('hotel_select_details hd', 'hd.EANHotelID=hs.EANHotelID', 'left')
                        ));
                        $all_wish_deals_array[$cookiekey['item_id']] = $deals_array;
                    }
                    if($cookiekey['item_name'] == 'hotels')
                    {
                        $this->db->order_by('hi.is_main', 'DESC');
                        $hotels_array = $this->app_model->find('hotels_select hs', 'first', array('hs.*', 'hd.*', 'hi.*'), array('hs.is_active' => 1, 'hs.EANHotelID' => $cookiekey['item_id']), array(
                            array('hotel_select_details hd', 'hd.EANHotelID=hs.EANHotelID', 'left'),
                            array('hotel_images hi', 'hs.EANHotelID=hi.EANHotelID', 'left')
                        ));

                        $all_wish_hotels_array[$cookiekey['item_id']] = $hotels_array;
                        /**
                         * added to solve image issues
                         */
                        if(isset($is_final_data[$cookiekey['item_id']]['image_path']) && !empty($is_final_data[$cookiekey['item_id']]['image_path'][0]['URL']))
                        {
                            $all_wish_hotels_array[$cookiekey['item_id']]['URL'] = $is_final_data[$cookiekey['item_id']]['image_path'][0]['URL'];
                        }
//                            pr($all_wish_hotels_array);
//                         pr($this->db->last_query());
                    }
                }
            }
            // pr($this->db->last_query());
            // pr($wishdata_array);
            $get_all_wishlist = array();
            $count = 0;

            foreach($wishdata_array as $cookiekey)
            {
                //pr($is_final_data,false);
                //echo $count;
                $get_all_wishlist[$count]['listid'] = $count;
                $get_all_wishlist[$count]['item_id'] = $cookiekey['item_id'];
                $get_all_wishlist[$count]['is_item'] = $cookiekey['item_name'];
                if($cookiekey['item_name'] == "deals")
                {

                    $get_all_wishlist[$count]['name'] = $cookiekey['item_id'];
                    $get_all_wishlist[$count]['promoDescription'] = $all_wish_deals_array[$cookiekey['item_id']]['promoDescription'];
                    $get_all_wishlist[$count]['in_this_life_desc'] = $is_final_data[$all_wish_deals_array[$cookiekey['item_id']]['hotelId']]['shortDescription'];
                    $get_all_wishlist[$count]['EANHotelID'] = $all_wish_deals_array[$cookiekey['item_id']]['hotelId'];
                    $get_all_wishlist[$count]['hotelid'] = $all_wish_deals_array[$cookiekey['item_id']]['hotelId'];
                    if(isset($all_wish_deals_array[$cookiekey['item_id']]['promoValue']) && !empty($all_wish_deals_array[$cookiekey['item_id']]['promoValue']))
                    {
                        //pr($all_wish_deals_array[$cookiekey['item_id']]['promoValue']) ;
                        $deals_amount = ceil ( floatval ( str_replace ( "-", "", $all_wish_deals_array[$cookiekey['item_id']]['promoValue'] ) ) * 100 );
                        if ( $all_wish_deals_array[$cookiekey['item_id']]['promoType'] == "PERCENTAGE" )
                        {
                            $final_discount = "Save " . $deals_amount . "%";
                        }
                        else
                        {
                            $final_discount = $all_wish_deals_array[$cookiekey['item_id']]['promoValue'] . " Off";
                        }
                        $get_all_wishlist[$count]['deals_price'] = $final_discount;
                    }
                    $get_all_wishlist[$count]['activities'] = $this->get_hotels_style($get_all_wishlist[$count]['hotelid']);

                    if(isset($is_final_data[$all_wish_deals_array[$cookiekey['item_id']]['hotelId']]['image_path'][0]['URL']))
                    {
                        $get_all_wishlist[$count]['URL'] = $is_final_data[$all_wish_deals_array[$cookiekey['item_id']]['hotelId']]['image_path'][0]['URL'];
                    }
                    else
                    {
                        $get_all_wishlist[$count]['URL'] = "";
                    }
                    $get_all_wishlist[$count]['expediaContent'] = $is_final_data[$all_wish_deals_array[$cookiekey['item_id']]['hotelId']];
                }
                if($cookiekey['item_name'] == "hotels")
                {
                    // pr($is_final_data);
                    $get_all_wishlist[$count]['name'] = $all_wish_hotels_array[$cookiekey['item_id']]['Name'];
                    $get_all_wishlist[$count]['promoDescription'] = "";
                    if(isset($is_final_data[$cookiekey['item_id']]))
                    {
                        $get_all_wishlist[$count]['in_this_life_desc'] = $is_final_data[$cookiekey['item_id']]['shortDescription'];
                    }
                    else
                    {
                        $get_all_wishlist[$count]['in_this_life_desc'] = "";
                    }
                    $get_all_wishlist[$count]['EANHotelID'] = $cookiekey['item_id'];
                    $get_all_wishlist[$count]['hotelid'] = $cookiekey['item_id'];
                    $get_all_wishlist[$count]['activities'] = $this->get_hotels_style($get_all_wishlist[$count]['hotelid']);
                    if(isset($all_wish_hotels_array[$cookiekey['item_id']]['URL']))
                    {
                        $get_all_wishlist[$count]['URL'] = $all_wish_hotels_array[$cookiekey['item_id']]['URL'];
                    }
                }
                if(isset($is_final_data[$cookiekey['item_id']]) && !empty($is_final_data[$cookiekey['item_id']]))
                {
                    $get_all_wishlist[$count]['expediaContent'] = $is_final_data[$cookiekey['item_id']];
                }

                $count++;
                //$get_all_wishlist = array();
            }
            //  pr( $get_all_wishlist );
        }
        else
        {
            $get_all_wishlist = array();
        }
        $this->data['get_all_wishlist'] = $get_all_wishlist;
        $this->template->write('title', 'User Wishlist' . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_description', 'User Wishlist' . " - " . COMPANY_TAGLINE);
        $this->template->write('meta_keywords', 'User Wishlist' . " - " . COMPANY_TAGLINE);
        $this->template->write_view('content', 'front/customer/my_wishlist', $this->data);
        $this->template->render();
    }

    public function get_search_list()
    {
        if(!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $destination_array = array();
        $post = $this->input->post();
        if(isset($post['search']) && !empty($post['search']))
        {

            $where = '';
            $post['search'] = addslashes(addslashes($post['search']));
            $where.="c.continent LIKE '%{$post['search']}%' OR c.continent LIKE '%{$post['search']}' OR  c.continent LIKE '{$post['search']}%' OR c.continent = '{$post['search']}'";
            $where.=" OR c.country_name LIKE '%{$post['search']}%' OR c.country_name LIKE '%{$post['search']}' OR c.country_name LIKE '{$post['search']}%' OR c.country_name = '{$post['search']}'";
            $where.=" OR city.city_name LIKE '%{$post['search']}%' OR city.city_name LIKE '%{$post['search']}' OR city.city_name LIKE '{$post['search']}%' OR c.country_name = '{$post['search']}'";
            $where_search = '('.$where.')' ;
            $this->db->where($where_search);
            $this->db->order_by('cont_name', 'ASC');
            $this->db->order_by('country_name', 'ASC');
            $query = $this->app_model->find('manage_search_destinations d', 'all',
                array('d.*', 'cc.name as cont_name', 'c.country_name as country_name', 'c.iso2','city.city_name'), array('d.is_active' => 1),
                array(
                    array('manage_continents cc', 'cc.id=d.continent_id AND cc.is_active=1 AND cc.is_deleted=0','LEFT'),
                    array('countries c', 'c.iso2=d.country_id AND c.is_active=1 AND c.is_deleted=0','LEFT'),
                    array('cities city', 'city.city_id=d.city_id AND city.is_active=1 AND city.is_deleted=0','LEFT')
                ));
            $this->data['search_cities'] = $this->get_search_destination_without_country();
            $this->data['search_country'] = $this->get_search_destination_without_continent();
            if(isset($query) && !empty($query))
            {
                foreach($query as $data)
                {
                    if(isset($data['city_name']) && !empty($data['city_name']))
                    {
                        $destination_array[$data['cont_name']]['city'][$data['city_name']] = $data['city_name'];
                    }
                    else
                    {
                        $destination_array[$data['cont_name']]['country'][$data['country_name']] = $data['country_name'];
                    }

                }
            }
            $this->data['destination_array'] = $destination_array;

            $hotel_array = array();
            $where = '';
            $where.="hs.Name LIKE '%{$post['search']}%' OR hs.Name LIKE '%{$post['search']}' OR hs.Name LIKE '{$post['search']}%' OR hs.Name = '{$post['search']}'";
            $this->db->where($where);
            $query = $this->app_model->find('hotels_select hs', 'list', array('hs.EANHotelID', 'hs.Name'), array('hs.is_active' => 1), array(array('select_hotel_styles shs', 'shs.EANHotelID=hs.EANHotelID', 'LEFT')));
            $this->data['hotellist'] = $query;
            $this->data['hotellistAjax'] = 1;
        }

        $html = $this->load->view('front/home/common/search_custom_list', $this->data, true);
        $data = array('html' => $html);
        echo json_encode($data);
        die;
    }

    public function get_hotel_info($hotelsId = null)
    {
        $call_api = $this->call_ean_info('info', selected_currency, $hotelsId, 'en_US', '2');
        pr(json_decode($call_api, true));
    }

}
