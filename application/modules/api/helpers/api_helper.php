<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	if (!function_exists('get_user_data')) {
		function get_user_data($userid = []){
			$CI = & get_instance();

			# Fetching user details
            $userContent    = $CI->Common->find([
                'table'     => USERS." User",
                'select'    => "User.id AS userid, User.profile_id,User.email email,
                                User.mobile, User.mobile AS mobileNumber, User.profile_img, 
                                User.email_verified,User.mobile_verified,User.status,  
                                Profile.fname AS firstName, 
                                Profile.lname AS lastName, 
                                Profile.gender, 
                                Profile.dob AS birthDate, 
                                Profile.zip AS zipCode,
                                Profile.state, 
                                Profile.city, 
                                Profile.country",
                'join'      => [[USER_PROFILE, 'Profile', 'INNER', "User.id = Profile.user_id"]],
                'where'     => "User.id = '{$userid}'",
                'query'     => 'first'
            ]);

            $userContent['profileImage'] = timthumb($userContent['profile_img'], 150, 150);

            return $result = [
            	'userdata'  => $userContent,
                # Generate JWT token
                'token'     => $CI->authorization_token->generateToken([
                    'id'         => $userid,
                    'email'      => $userContent['email'],
                    'profile_id' => $userContent['profile_id'],
                    'mobile'     => $userContent['mobile'],
                    'time'       => time()
                ])
            ];
	    }
	}

    if (!function_exists('get_customer_data')) {
        function get_customer_data($userid = []){
            $CI = & get_instance();

            # Fetching user details
            $userContent    = $CI->Common->find([
                'table'     => USERS." User",
                'select'    => "User.id, User.profile_id,User.email email,
                                User.mobile mobileNumber, User.profile_img, 
                                User.email_verified,User.mobile_verified,User.status,  
                                Profile.fname AS firstName, 
                                Profile.lname AS lastName, 
                                Profile.gender, 
                                Profile.dob AS birthDate, 
                                Profile.street_address, 
                                Profile.city, 
                                Profile.state, 
                                Profile.country,
                                Profile.zip AS zipCode",
                'join'      => [[USER_PROFILE, 'Profile', 'INNER', "User.id = Profile.user_id"]],
                'where'     => "User.id = '{$userid}'",
                'query'     => 'first'
            ]);

            $userContent['profileImage'] = timthumb($userContent['profile_img'], 150, 150);
            $addressArr = [
                @$userContent['street_address'],
                @$userContent['city'],
                @$userContent['state'],
                @$userContent['country'],
                @$userContent['zipCode']
            ];
            $userContent['full_address'] = implode(', ', array_filter($addressArr));
            return $result = [
                'userdata'  => $userContent,
                # Generate JWT token
                'token'     => $CI->authorization_token->generateToken([
                    'id'         => $userid,
                    'email'      => $userContent['email'],
                    'profile_id' => $userContent['profile_id'],
                    'mobile'     => $userContent['mobileNumber'],
                    'time'       => time()
                ])
            ];
        }
    }

    if (!function_exists('category_services')) {
        function category_services($salon_id = NULL, $parentId = 0){
            $result = [];
            $CI = & get_instance();

            # Fetch salon categories
            $data = $CI->Common->find([
                'table'     => CATEGORIES." Category",
                'select'    => "Category.id, Category.slug, Category.title, Category.logo, Category.parent_id AS mainCategoryID",
                'join'      => [[SALON_CATEGORY, 'Rel', 'INNER', "Rel.category_id = Category.id AND Rel.salon_id = '{$salon_id}'"]],
                'where'     => "type='s' AND parent_id = '{$parentId}' AND status = 'active'"
            ]);

            if(!empty($data)){
                foreach($data AS $key => $eachData){
                    $result[$key] = $eachData;
                    $result[$key]['mainCategoryIconUrl']    = (@$eachData['logo'] != "" && file_exists(UPLOADS_REAL_PATH.@$eachData['logo'])) ? UPLOAD_PATH.$eachData['logo'] : DEFAULT_IMAGE;
                    $result[$key]['count']                  = 0;
                    $result[$key]['actualServices']         = [];
                    unset($result[$key]['logo']);

                    # Fetch actual services
                    $service_cat_id = @$eachData['id'];
                    $actualService = $CI->Common->find([
                        'table'     => SERVICES_TYPE,
                        'select'    => "id AS actualServiceID, type_name, time, time_unit, rate, 0 AS qty",
                        'where'     => "salon_id = '{$salon_id}' AND service_cat_id = '{$service_cat_id}'"
                    ]);
                    
                    $actualServices = [];
                    if(!empty($actualService)){
                        foreach($actualService AS $actKey => $eachActual){
                            $eachActual['qty'] = (int) $eachActual['qty'];
                            $actualServices[$actKey] = $eachActual;
                        }

                        $result[$key]['count'] = count($actualServices);
                        $result[$key]['actualServices'] = $actualServices;
                    }

                    $sub = category_services($salon_id, @$eachData['id']);
                    if(!empty($sub)){
                        $result[$key]['subServicesCategory'] = $sub;
                    }
                }
            }

            return $result;
        }
    }

    if (!function_exists('service_categories_block_menu')) {
        function service_categories($parentId = 0, $salonId = NULL, $selectedParent = NULL, $selectedChild = NULL, $level = 1){
            $maxLevel = 2; 
            if($level <= $maxLevel){ 
                $result = "";
                $CI = & get_instance();
                $join = [
                    [
                        'table' => SALON_CATEGORY,
                        'alias' => 'Rel',
                        'type'  => 'INNER',
                        'conditions' => ("Rel.category_id = Category.id AND Rel.salon_id = '{$salonId}'")
                    ]
                ];
                $data = $CI->Common->find(CATEGORIES.' Category', "Category.id, Category.slug, Category.parent_id, Category.title, Category.status", $join, "parent_id = '{$parentId}' AND type = 's'", "title ASC");
                if(count($data) > 0){
                    $open = ($selectedParent == @$data[0]['parent_id'] || $selectedChild == @$data[0]['parent_id']) ? 'open' : '';
                    echo "<ul class='menu-level-{$level} {$open}'>";
                    foreach($data AS $key => $eachData){
                        $active = ($selectedChild == $eachData['id']) ? 'active' : '';
                        echo "<li class='menu-li-level-{$level}'>";
                            echo "<label class='label-level-{$level} {$active}'>";
                                if($level < $maxLevel){
                                    echo "<a href='javascript:void(0)' class='parent-services'><i class='fa fa-caret-right' ></i> {$eachData['title']} </a>";
                                }else{
                                    echo "<a href='".SALON_URL."services/".$eachData['slug']."/list'><i class='fa fa-caret-right' ></i> {$eachData['title']} </a>";
                                }
                                if($level < $maxLevel){
                                    $child = $CI->Common->find(CATEGORIES, "COUNT(id) AS total", "", "parent_id = '{$eachData['id']}'");
                                }
                                echo "<sapn class='level-actions'>";
                                    if(@$child[0]['total'] > 0){                        
                                        echo "<a href='javascript:void(0)' class='parent-services'><i class='fa fa-angle-down'></i></a>";
                                    }
                                echo "</span>";
                            echo "</label>";

                            $sub = service_categories_block_menu(@$eachData['id'], $salonId, $selectedParent, $selectedChild, $level + 1);
                            if($sub){
                                echo $sub;
                            }
                        echo "</li>";
                    }
                    echo "</ul>";
                }
            }
        }
    }

/* End of file api_helper.php */
/* Location: ./application/helpers/common_helper.php */