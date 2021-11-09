<?php



    class Home_model extends CI_Model

    {

        public function __consruct()

        {

            parent::__construct();

            $this->load->database();

        } 



        public function category()

        {   

            $result=$this->db->get_where('rr_categories', array('status' => 'A'))->result_array();

            return $result;

        }



        public function finest_product()

        {



            $result=$this->db->select('*')

            ->from('rr_products')

            ->where('rr_products.status', 'A')

            ->where('rr_product_skus.popular_product',1)

            ->join('rr_product_skus', 'rr_product_skus.product_id = rr_products.id', 'LEFT')

            ->get()->result_array();

            return $result;

        }



        public function recomended_product()

        {



            $result=$this->db->select('*')

            ->from('rr_products')

            ->where('rr_products.status', 'A')

            ->where('rr_product_skus.recomended_product',1)

            ->join('rr_product_skus', 'rr_product_skus.product_id = rr_products.id', 'LEFT')

            ->get()->result_array();

            return $result;

        }



        public function find_images($productImageId)

        {

            $productImageId=explode(",",$productImageId);

            $result=$this->db->select('*')->from(IMAGES)->where_in('id',$productImageId)->get()->result_array();

            return $result;

        }





        public function category_products($urlparam)

        {

            $result=$this->db->select('*')

                ->from(CATEGORIES)

                ->join(PRODUCT_CATEGORIES,CATEGORIES.'.id='.PRODUCT_CATEGORIES.'.category_id')

                ->join(PRODUCTS,PRODUCT_CATEGORIES.'.product_id='.PRODUCTS.'.id')

                ->join(PRODUCT_SKUS,PRODUCT_SKUS.'.product_id='.PRODUCTS.'.id')

                ->where(CATEGORIES.'.slug',$urlparam)

                ->get()

                ->result_array();



           // return last_query();

           return $result;

        }



        public function wisthlist_count()

        {

            if(isset($this->session->userdata['customer_sess']['id']) && $this->session->userdata['customer_sess']['id']!="")

            {

                $count=$this->db->select('*')->from(WISHLIST)->where(

                    [

                        'user_id'=>$this->session->userdata['customer_sess']['id']

                    ])->get()->result_array();

            }

            else

            {

                $count=[];

            }

            return count($count);

        }





        public function cartCount()

        {

                //print_r($_SESSION);

                if(isset($this->session->userdata['customer_sess']['id']) && $this->session->userdata['customer_sess']['id']!="")

                {

                    $returnCartCount=$this->db->select("*")

                    ->from(CART_ITEMS)

                    ->where(['user_id'=>$this->session->userdata['customer_sess']['id']])

                    ->get()

                    ->result_array();

                    $cartCount=count($returnCartCount);

                }

                else if(!isset($this->session->userdata['customer_sess']) && isset($_SESSION['user_id']))

                {

                    $returnCartCount=$this->db->select("*")

                    ->from(CART_ITEMS)

                    ->where(['user_id'=>$_SESSION['user_id']])

                    ->get()

                    ->result_array();

                    $cartCount=count($returnCartCount);

                }

                else

                {

                    $cartCount=0;

                }



            return $cartCount;

        }

    }



?>