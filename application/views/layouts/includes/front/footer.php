
<div class="getusSomeFeedArea">
    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <div class="feedCol">
                    <h2>Give us some feedback!</h2>
                    <p>What do you think of this page? How can we improve it?</p>
                    <form>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">COMMENTS</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">EMAIL ADDRESS</label>
                            <input type="text" name="" class="form-control">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn">GIVE FEEDBACK</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="contactTxt">
                    <div class="conttactLgo">
                        <a href="#"><img src="<?=FRONT_ASSETS?>images/logo.png" alt=""></a>
                    </div>
                    <?=get_content('footer-content')?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="newsLtr">
    <div class="container">
        <div class="newsRap">
            <div class="row">
                <div class="col-sm-6">
                    <div class="newsTxt">
                        <?php $newsLetter = get_content('newsletter-sign-up', false); ?>
                        <h3><?=@$newsLetter['page_title']?></h3>
                        <?=@$newsLetter['content']?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="newsFrmCol">
                        <input type="email" name="" class="form-control" placeholder="Your Email Address">
                        <button class="btn">SUBSCRIBE</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="ftrMin">
    <div class="container">
        <ul class="ftrMnu">
            <li><a href="#">Track Order</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">Conditions of Use & Returns</a></li>
            <li><a href="#">Privacy Notice</a></li>
            <li><a href="#">Shipping</a></li>
        </ul>
        <ul class="pament-method">
            <li><img src="<?=FRONT_ASSETS?>images/pament-method.png" alt=""></li>
        </ul>
    </div>
    <div class="cpyArea">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="ftrBtm">

                        <div class="cpy"><span>Copyright 2019 |  all right reserved Retractable Reels</span><span>Website designed and developed by<a target="_blank" href="https://www.ivaninfotech.com"> Ivan Infotech</a></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>