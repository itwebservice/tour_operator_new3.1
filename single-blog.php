<?php

include 'config.php';

include BASE_URL . 'model/model.php';

//Include header

include 'layouts/header2.php';
$_SESSION['page_type'] = 'blog-inner';

$blog_id = $_GET['blog_id'];

$blogData = $moduleData->getBlogData($blog_id);

?>


<!-- ********** Component :: Page Title ********** -->

<div class="c-pageTitleSect ts-pageTitleSect">

    <div class="container">

        <div class="row">

            <div class="col-md-7 col-12">



                <!-- *** Search Head **** -->

                <div class="searchHeading">

                    <span class="pageTitle mb-0">Blog Details</span>

                </div>

                <!-- *** Search Head End **** -->

            </div>



            <div class="col-md-5 col-12 c-breadcrumbs">

                <ul>

                    <li>

                        <a href="<?= BASE_URL_B2C ?>">Home</a>

                    </li>

                    <li class="st-active">

                        <a href="javascript:void(0)">Blog Details</a>

                    </li>

                </ul>

            </div>



        </div>

    </div>

</div>

<!-- ********** Component :: Page Title End ********** -->

<!-- Landing Section Start -->
<!-- 
<section class="ts-inner-landing-section ts-font-poppins">

    <img src="../images/banner-2.jpg" alt="" class="img-fluid">

    <div class="ts-inner-landing-content">

        <div class="container">

            <h1 class="ts-section-title">BLOGS DETAILS</h1>

        </div>

    </div>

</section> -->

<!-- Landing Section End -->



<!-- Contact Section Start -->

<section class="ts-contact-section">

    <div class="container">

        <div class="ts-blog-content ts-single-blog-content">

            <div class="row">

                <div class="col col-12 col-md-12 col-lg-12">

                    <div class="ts-blog-card">

                        <div class="ts-blog-card-img">
                            <?php if ($blogData && $blogData['url']) : ?>
                                <img src="<?php echo $blogData['url']; ?>" alt="Blog Image" class="img-fluid">
                            <?php endif; ?>

                        </div>

                        <div class="ts-blog-card-body">

                            <!-- <div class="ts-blog-info">

                                <p class="ts-blog-time">

                                    <i class="fa fa-calendar" aria-hidden="true"></i>

                                    <span>ABC</span>

                                </p>

                                <p class="ts-blog-time">

                                    <i class="fa fa-tags" aria-hidden="true"></i>

                                    <span>Tags Works, Personal</span>

                                </p>

                                <p class="ts-blog-time ml-auto">

                                    <i class="fa fa-comments-o" aria-hidden="true"></i>

                                    <span>33</span>

                                </p>

                            </div> -->

                            <h3 class="ts-blog-card-title"><?php if ($blogData && $blogData['title']) {
                                                                echo $blogData['title'];
                                                            }  ?></h3>

                            <p class="ts-blog-card-description">
                            <div class="custom_texteditor"><?php if ($blogData && $blogData['description']) {
                                                                echo $blogData['description'];
                                                            }  ?></div>
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- Contact Section End -->

<?php include 'layouts/footer2.php'; ?>



<script type="text/javascript" src="js/scripts.js"></script>