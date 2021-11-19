<aside class="left-side sidebar-offcanvas">



    <section class="sidebar">



        <ul class="sidebar-menu">

            <li>
                <a href="<?= base_url('admincp');?>">
                    <i class="fa fa-home"></i>Home</a>
            </li>
            <?php
            $admin_sess = $this->session->userdata('admin_sess');
            if($admin_sess['role'] == 1)
            {
               ?>
               <li>
                <a href="javascript:void(0)" class="dropdown-btn <?php echo (@$this->uri->segment(2) =='stores')? "active":'' ?>"><i class="fa fa-gear"></i>Rooms</a>
                <ul class="dropdown-container <?php echo (@$this->uri->segment(2) =='stores')? "open":'' ?>">
                    <li>
                        <a href="<?= ADMIN_URL."rooms/all" ?>">Rooms</a>
                    </li>
                   
                </ul>
            </li>
            
          
            <li>
                <a href="javascript:void(0)" class="dropdown-btn <?php echo (@$this->uri->segment(2) =='user')? "active":'' ?>"><i class="fa fa-user"></i>User</a>
                <ul class="dropdown-container <?php echo (@$this->uri->segment(2) =='user')?"open":'' ?>">
                    <li>
                        <a href="<?= ADMIN_URL."user/all" ?>">Users</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:void(0)" class="dropdown-btn <?php echo (@$this->uri->segment(2) =='user')? "active":'' ?>"><i class="fa fa-user"></i>Gallery</a>
                <ul class="dropdown-container <?php echo (@$this->uri->segment(2) =='gallery')?"open":'' ?>">
                    <li>
                        <a href="<?= ADMIN_URL."gallery/all" ?>">Galley</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:void(0)" class="dropdown-btn <?php echo (@$this->uri->segment(2) =='user')? "active":'' ?>"><i class="fa fa-user"></i>Pages</a>
                <ul class="dropdown-container <?php echo (@$this->uri->segment(2) =='pages')?"open":'' ?>">
                    <li>
                        <a href="<?= ADMIN_URL."pages/about-us" ?>">About us</a>
                    </li>
                    <li>
                        <a href="<?= ADMIN_URL."pages/contact-us" ?>">Contact us</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:void(0)" class="dropdown-btn <?php echo (@$this->uri->segment(2) =='user')? "active":'' ?>"><i class="fa fa-user"></i>Site settings</a>
                <ul class="dropdown-container <?php echo (@$this->uri->segment(2) =='site-settings')?"open":'' ?>">
                    <li>
                        <a href="<?= ADMIN_URL."site-settings" ?>">Site settings</a>
                    </li>
                </ul>
            </li>
               <?php
            }

            ?>
           
            

            <li>
                <a href="javascript:void(0)" class="dropdown-btn <?php echo (@$this->uri->segment(2) =='ride')? "active":'' ?>"><i class="fa fa-building"></i>Bookings</a>
                <ul class="dropdown-container <?php echo (@$this->uri->segment(2) =='ride')?"open":'' ?>">
                    <li>
                        <a href="<?= ADMIN_URL."bookings/all" ?>">Bookings</a>
                    </li>
                    <!-- <li>
                        <a href="<?= ADMIN_URL."ride/global-rate" ?>">Global Rate</a>
                    </li> -->
                </ul>
            </li>

 </section>



</aside>







<script>



    var dropdown = document.getElementsByClassName("dropdown-btn");



    var i;







    for (i = 0; i < dropdown.length; i++) {



        dropdown[i].addEventListener("click", function() {



            this.classList.toggle("active");



            var dropdownContent = this.nextElementSibling;



            dropdownContent.classList.toggle("open");



        });



    }



</script>



              