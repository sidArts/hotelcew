<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Categories extends MX_Controller {



    private $data;

    public function __construct()  

    { 

    	parent::__construct();

    	$this->layout->set("admin-panel");

    	$this->data = [];

    }



    public function list(){

        $breadcrumb = [

            [

                'page' => "Products",

                'url' => ADMIN_URL.'products/product-list'

            ],[

                'page' => "Varaitions"

            ]

        ];

        $this->data['categories'] = $this->Common->find(CATEGORIES, "id, title", "", "status = 'A' AND parent_id IS NULL");

        #pre(last_query(), 1);

        $this->layout->set_breadcumb($breadcrumb);        

        $this->layout->set_title("Product Categories");

    	$this->layout->view('admin/product-categories', $this->data);

	}



    public function load(){

        $inputs = $this->input->post();

        $conditions = "Cat.cat_type = 'product'";

        $start      = 0; $limit = 10; 

        $column     = @$inputs['columns'][@$inputs['order'][0]['column']]['data'];

        $dir        = @$inputs['order'][0]['dir'];

        $order      = $column." ".$dir;



        if(@$inputs['start'] > 0){

            $start = $inputs['start'];

        }



        if(@$inputs['length'] > 0){

            $limit = $inputs['length'];

        }



        if(@$inputs['search']['value'] != ""){

            $search         = $inputs['search']['value'];

            $conditions    .= " AND (Cat.title LIKE '%{$search}%') "; 

        }



        // Parent filter

        if(@$inputs['parent_category'] != ""){

            $parent_category = $inputs['parent_category'];

            $conditions     .= " AND Cat.parent_id = '{$parent_category}'"; 

        }



        $join = [

            [

                'table' => CATEGORIES, 

                'alias' => 'Parent',

                'type' => 'LEFT',

                'conditions' => "Parent.id = Cat.parent_id"

            ]

        ];

        $categories = $this->Common->find(CATEGORIES." Cat", "Cat.*, Parent.title AS parent_title", $join, $conditions, 

                                    $order, 

                                    "", 

                                    "{$start}, {$limit}");

        #pre(last_query());

        #pre($products, 1);

        $catList['data'] = [];

        foreach($categories AS $key => $eachCat){

            $encId = encrypt($eachCat['id']);

            $statusBtn  = ($eachCat['status'] == 'A') ? 'success' : 'danger';

            $statusLogo = ($eachCat['status'] == 'A') ? 'check' : 'ban';

            $catList['data'][$key]['id']       = "<label for='{$encId}'></label><input type='checkbox' id='{$encId}' data-status='".$eachCat['status']."' class='iCheck' value='".$encId."'>";

            

            $catList['data'][$key]['title']        = $eachCat['title'];

            $catList['data'][$key]['parent_title'] = ($eachCat['parent_title']) ? $eachCat['parent_title'] : "N/A";



            $action = " <span class='btn-action'>

                            <a class='btn btn-{$statusBtn} confirm-status-change' data-status1='{$eachCat['status']}' data-constant='CATEGORIES' data-id='{$encId}' href='#' title='".@STATUS[$eachCat['status']]."'><i class='fa fa-{$statusLogo}'></i></a>

                            <a class='btn btn-primary' href='".ADMIN_URL.'products/categories/update/'.encrypt($eachCat['id'])."' title='Edit'><i class='fa fa-pencil'></i></a>

                            <a class='btn btn-danger confirm-delete-btn' data-drop='0' data-constant='CATEGORIES' data-id='{$encId}' href='javascript:void(0)' title='Delete'><i class='fa fa-trash-o'></i></a>

                        </span>";

            $catList['data'][$key]['action']   = $action;

        }



        //$recordsFiltered = count($products);

        $recordsFiltered = $this->Common->count(CATEGORIES.' Cat', $conditions);

        $catList['recordsFiltered'] = $recordsFiltered;



        $recordsTotal = $this->Common->count(CATEGORIES);

        $catList['recordsTotal'] = $recordsTotal;

        echo json_encode($catList);

    }



    public function create(){

        $breadcrumb = [

            [

                'url' => ADMIN_URL.'products/product-list',

                'page' => "Products"

            ],[

                'page' => "Categories",

                'url' => ADMIN_URL."products/categories/"

            ],[

                'page' => "New Category"

            ]

        ];



        $this->layout->set_breadcumb($breadcrumb); 

        $this->data['parents'] = $this->Common->find(CATEGORIES, "id, title", "", "status = 'A' AND parent_id IS NULL");

        $this->data['content'] = [];

        $this->layout->set_title("Add Product");

        $this->layout->view('admin/category-edit', $this->data);

    }



    public function edit($catId = NULL){

        $catId = decrypt($catId);

        $breadcrumb = [

            [

                'url' => ADMIN_URL.'products/product-list',

                'page' => "Products"

            ],[

                'page' => "Categories",

                'url' => ADMIN_URL.'products/categories'

            ],[

                'page' => "Update Category"

            ]

        ];



        $this->layout->set_breadcumb($breadcrumb);

        $this->data['parents'] = $this->Common->find(CATEGORIES, "id, title", "", "status = 'A' AND id != '{$catId}' AND parent_id IS NULL");

        $this->data['content'] = $content = $this->Common->findById(CATEGORIES, $catId);

        if(empty($content)) { show_404();}

        $this->layout->set_title("Update Category");

        $this->layout->view('admin/category-edit', $this->data);

    }



    public function details($productSlug = NULL){

        $breadcrumb = [

            [

                'url' => BASE_URL.'products',

                'page' => "Products"

            ],[

                'page' => "Details"

            ]

        ];

        $this->layout->set_breadcumb($breadcrumb);

        $this->data['content'] = $content = $this->Common->findBy('rr_products_bkp', "slug", $productSlug);

        if(empty($content)) { show_404();}

        $this->layout->set_title("Product Details");

        $this->layout->view('product-details', $this->data);

    }



    public function save(){        

        if($this->input->post()){

            $data = $this->input->post();

            #pre($data, 1);

            $contentId  = decrypt($data['id']);

            $catDtls = [];

            if($data['id']){

                $catDtls = $this->Common->findById(CATEGORIES, $contentId);

                if(empty($catDtls)){

                    #die(json_encode(['status' => 0, 'msg' => "Please try again"]));

                    set_toastr("Category not found");

                }

            }



            $content = [

                'id'                => $contentId,

                'parent_id'         => (@$data['parent_id']) ? $data['parent_id'] : NULL,

                'title'             => @$data['title'],

                'meta_title'        => @$data['meta_title'],

                'meta_keyword'      => @$data['meta_keyword'],

                'meta_desc'         => @$data['meta_desc'],

                'description'       => addslashes(@$data['description']),

                'status'            => (@$data['status']) ? 'A' : 'I',
                
                'downloadable'            => (@$data['downloadable']) ? $data['downloadable'] : NULL,


            ];



            if($contentId == ""){

                $content['slug'] = slug(CATEGORIES, 'slug', $data['title']);

            }



            if(@$data['cropper_delete'] > 0){

                @unlink(UPLOADS_REAL_PATH.$catDtls['image']);

                $content['image'] = "";

            }

            if($data['image_code'] != ""){

                $targetPath = "images/categories/";

                $file = upload_image($targetPath, $data['image_code'], clean(@$data['title']).'-'.time());

                $content['image'] = $targetPath.$file;

            }

            //pre($product, 1);

            if($catId = $this->Common->save(CATEGORIES, $content)){

                /*die(json_encode([

                        'status'    => 1, 

                        'id'        => encrypt($catId),

                        'msg'       => "Category saved successfully"

                    ]

                ));*/

                set_toastr("Category saved successfully");

            }

        }else{

            set_toastr("Please try again", 0);

        }

        #die(json_encode(['status' => 0, 'msg' => "Please try again"]));



        redirect(ADMIN_URL.'products/categories/update/'.encrypt($catId));

    }



    public function image(){

        $breadcrumb = [

            [

                'url' => ADMIN_URL.'products/product-list',

                'page' => "Products"

            ],[

                'page' => "Image"

            ]

        ];

        $this->layout->set_breadcumb($breadcrumb);

        $this->layout->set_title("Update Product");

        $this->layout->view('admin/image-upload', $this->data);

    }

}

