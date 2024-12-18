<?php

/**
 * @package     Joomla.Site
 * @subpackage  Templates.cassiopeia
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Document\HtmlDocument $this */

$app   = Factory::getApplication();
$input = $app->getInput();
$wa    = $this->getWebAssetManager();

// Browsers support SVG favicons
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon.svg', '', [], true, 1), 'icon', 'rel', ['type' => 'image/svg+xml']);
$this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon-pinned.svg', '', [], true, 1), 'mask-icon', 'rel', ['color' => '#000']);

// Detecting Active Variables
$option   = $input->getCmd('option', '');
$view     = $input->getCmd('view', '');
$layout   = $input->getCmd('layout', '');
$task     = $input->getCmd('task', '');
$itemid   = $input->getCmd('Itemid', '');
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$menu     = $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';


// Color Theme
$paramsColorName = $this->params->get('colorName', 'colors_standard');
$assetColorName  = 'theme.' . $paramsColorName;

// Use a font scheme if set in the template style options
$paramsFontScheme = $this->params->get('useFontScheme', false);
$fontStyles       = '';

if ($paramsFontScheme) {
    if (stripos($paramsFontScheme, 'https://') === 0) {
        $this->getPreloadManager()->preconnect('https://fonts.googleapis.com/', ['crossorigin' => 'anonymous']);
        $this->getPreloadManager()->preconnect('https://fonts.gstatic.com/', ['crossorigin' => 'anonymous']);
        $this->getPreloadManager()->preload($paramsFontScheme, ['as' => 'style', 'crossorigin' => 'anonymous']);
        $wa->registerAndUseStyle('fontscheme.current', $paramsFontScheme, [], ['rel' => 'lazy-stylesheet', 'crossorigin' => 'anonymous']);

        if (preg_match_all('/family=([^?:]*):/i', $paramsFontScheme, $matches) > 0) {
            $fontStyles = '--cassiopeia-font-family-body: "' . str_replace('+', ' ', $matches[1][0]) . '", sans-serif;
			--cassiopeia-font-family-headings: "' . str_replace('+', ' ', isset($matches[1][1]) ? $matches[1][1] : $matches[1][0]) . '", sans-serif;
			--cassiopeia-font-weight-normal: 400;
			--cassiopeia-font-weight-headings: 700;';
        }
    } else {
        $wa->registerAndUseStyle('fontscheme.current', $paramsFontScheme, ['version' => 'auto'], ['rel' => 'lazy-stylesheet']);
        $this->getPreloadManager()->preload($wa->getAsset('style', 'fontscheme.current')->getUri() . '?' . $this->getMediaVersion(), ['as' => 'style']);
    }
}

// Enable assets
$wa->usePreset('template.cassiopeia.' . ($this->direction === 'rtl' ? 'rtl' : 'ltr'))
    ->useStyle('template.active.language')
    ->registerAndUseStyle($assetColorName, 'global/' . $paramsColorName . '.css')
    ->useStyle('template.user')
    ->useScript('template.user')
    ->addInlineStyle(":root {
		--hue: 214;
		--template-bg-light: #f0f4fb;
		--template-text-dark: #495057;
		--template-text-light: #ffffff;
		--template-link-color: var(--link-color);
		--template-special-color: #001B4C;
		$fontStyles
	}");

// Override 'template.active' asset to set correct ltr/rtl dependency
$wa->registerStyle('template.active', '', [], [], ['template.cassiopeia.' . ($this->direction === 'rtl' ? 'rtl' : 'ltr')]);

// Logo file or site title param
if ($this->params->get('logoFile')) {
    $logo = HTMLHelper::_('image', Uri::root(false) . htmlspecialchars($this->params->get('logoFile'), ENT_QUOTES), $sitename, ['loading' => 'eager', 'decoding' => 'async'], false, 0);
} elseif ($this->params->get('siteTitle')) {
    $logo = '<span title="' . $sitename . '">' . htmlspecialchars($this->params->get('siteTitle'), ENT_COMPAT, 'UTF-8') . '</span>';
} else {
    $logo = HTMLHelper::_('image', 'logo.svg', $sitename, ['class' => 'logo d-inline-block', 'loading' => 'eager', 'decoding' => 'async'], true, 0);
}

$hasClass = '';

if ($this->countModules('sidebar-left', true)) {
    $hasClass .= ' has-sidebar-left';
}

if ($this->countModules('sidebar-right', true)) {
    $hasClass .= ' has-sidebar-right';
}

// Container
$wrapper = $this->params->get('fluidContainer') ? 'wrapper-fluid' : 'wrapper-static';

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');

$stickyHeader = $this->params->get('stickyHeader') ? 'position-sticky sticky-top' : '';

// Defer fontawesome for increased performance. Once the page is loaded javascript changes it to a stylesheet.
$wa->getAsset('style', 'fontawesome')->setAttribute('rel', 'lazy-stylesheet');

$activeTemplate = $app->getTemplate(true)->template;
$template_path  = JURI::root().'templates/'.$activeTemplate.'/';

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <jdoc:include type="metas" />
    <!--<jdoc:include type="styles" />
    <jdoc:include type="scripts" />-->
    <!-- Meta Tags -->
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta name="description" content=" - CSDL Khoa học"/>
    <meta name="keywords" content=" CSDL Khoa học"/>
    <meta name="author" content="dxnguyen@gmail.com"/>

    <!-- Page Title -->
    <title>Trang chủ - CSDL Khoa học</title>

    <!-- Favicon and Touch Icons -->
    <link href="<?php echo $template_path?>csdl/images/favicon.ico" rel="shortcut icon" type="image/png">
    <link href="<?php echo $template_path?>csdl/images/apple-touch-icon.png" rel="apple-touch-icon">
    <link href="<?php echo $template_path?>csdl/images/apple-touch-icon-72x72.png" rel="apple-touch-icon" sizes="72x72">
    <link href="<?php echo $template_path?>csdl/images/apple-touch-icon-114x114.png" rel="apple-touch-icon" sizes="114x114">
    <link href="<?php echo $template_path?>csdl/images/apple-touch-icon-144x144.png" rel="apple-touch-icon" sizes="144x144">

    <!-- Stylesheet -->
    <link type="text/css" href="<?php echo $template_path?>assets/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="<?php echo $template_path?>csdl/css/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $template_path?>csdl/css/animate.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $template_path?>csdl/css/css-plugin-collections.css" rel="stylesheet"/>
    <!-- CSS | menuzord megamenu skins -->
    <link id="menuzord-menu-skins" href="<?php echo $template_path?>csdl/css/menuzord-skins/menuzord-rounded-boxed.css"
          rel="stylesheet"/>
    <!-- CSS | Main style file -->
    <link href="<?php echo $template_path?>csdl/css/style-main.css" rel="stylesheet" type="text/css">
    <!-- CSS | Custom Margin Padding Collection -->
    <link href="<?php echo $template_path?>csdl/css/custom-bootstrap-margin-padding.css" rel="stylesheet" type="text/css">
    <!-- CSS | Responsive media queries -->
    <link href="<?php echo $template_path?>csdl/css/responsive.css" rel="stylesheet" type="text/css">
    <!-- CSS | Style css. This is the file where you can place your own custom css code. Just uncomment it and use it. -->
    <!-- <link href="https://csdlkhoahoc.hueuni.edu.vn/csdl/css/style.css" rel="stylesheet" type="text/css"> -->

    <!-- CSS | Theme Color -->
    <link href="<?php echo $template_path?>csdl/css/colors/theme-skin-color-set-1.css" rel="stylesheet" type="text/css">


    <style type="text/css">
        .unis {
            width: 555px;
            height: 340px;
            position: relative;
            content: '';
            background: url(<?php echo $template_path?>csdl/images/vnmap.png) no-repeat top left;
            background-size: contain;
        }

        .unis img {
            max-height: 70px;
        }

        .unis a {
            margin-left: 5px;
        }

        .uni-item-r1,
        .uni-item-r2,
        .uni-item-r3{
            position: absolute;
            display: block;
            z-index: 9;
        }

        .uni-item-r1 {
            top: 50px;
        }

        .uni-item-r2 {
            top: 165px;
        }

        .uni-item-r3{
            top: 235px;
        }

        .uni-item-r1 a:first-child {
            margin-left: 18px;
        }
        .uni-item-r1 a:nth-child(3) {
            margin-left: 135px;
        }

        .uni-item-r2 a:first-child {
            margin-left: 0px;
        }
        .uni-item-r2 a:nth-child(4) {
            margin-left: 90px;
        }

        .uni-item-r3 a:first-child{
            margin-left: 33px;
        }
        .uni-item-r3 a:nth-child(2){
            margin-left: 10px;
        }
        .uni-item-r3 a:nth-child(3){
            margin-left: 108px;
        }
        .hue-uni {
            position: absolute;
            z-index: 10;
            top: 105px;
            left: 230px;
        }

        .hue-uni img {
            max-height: 70px;
        }
    </style>


    <!-- external javascripts -->
    <script src="<?php echo $template_path?>csdl/js/jquery-2.2.4.min.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="https://csdlkhoahoc.hueuni.edu.vn/assets/html5shiv/dist/html5shiv.min.js"></script>
    <script type="text/javascript" src="https://csdlkhoahoc.hueuni.edu.vn/assets/respond/dest/respond.min.js"></script>
    <![endif]-->
</head>

<body class="site <?php echo $option
    . ' ' . $wrapper
    . ' view-' . $view
    . ($layout ? ' layout-' . $layout : ' no-layout')
    . ($task ? ' task-' . $task : ' no-task')
    . ($itemid ? ' itemid-' . $itemid : '')
    . ($pageclass ? ' ' . $pageclass : '')
    . $hasClass
    . ($this->direction == 'rtl' ? ' rtl' : '');
?>">
<div id="wrapper" class="clearfix">

    <!-- Header -->
    <header id="header" class="header">
        <div class="header-middle p-0 bg-lightest xs-text-center">
            <div class="container pt-0 pb-0">
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-md-3 text-left">
                        <div class="widget no-border m-0">
                            <a class=" menuzord-menu flip xs-pull-center mt-10 mb-10" href="javascript:void(0)">
                                <img src="<?php echo $template_path?>csdl/images/logo.png" alt="">
                            </a>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-8 col-md-9">
                        <div class="widget mt-10 pull-left sm-pull-none sm-text-center">
                            <h3 class="font-weight-800 text-theme-colored font-27">CSDL KHOA HỌC VÀ CÔNG NGHỆ</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-nav">
            <div class="header-nav-wrapper navbar-scrolltofixed bg-theme-colored border-bottom-theme-color-2-1px">
                <div class="container">
                    <nav id="menuzord" class="menuzord bg-theme-colored pull-left flip menuzord-responsive">
                        <ul class="menuzord-menu">
                            <li class="active">
                                <a href="index-2.html">Trang chủ</a>
                            </li>
                            <li >
                                <a href="index.php/scientist.html">Nhà khoa học</a>
                            </li>
                            <li >
                                <a href="index.php/topic.html">Đề tài khoa học</a>
                            </li>
                            <li >
                                <a href="index.php/article.html">Bài báo khoa học</a>
                            </li>
                            <li >
                                <a href="index.php/book.html">Sách và giáo trình</a>
                            </li>
                            <li class="">
                                <a href="javascript:;">Sản phẩm khác</a>
                                <ul class="dropdown">
                                    <li >
                                        <a href="index.php/certificate.html">Sở hữu trí tuệ</a>
                                    </li>
                                    <li >
                                        <a href="index.php/construction.html">Công trình thực tiễn</a>
                                    </li>
                                    <li >
                                        <a href="index.php/award.html">Giải thưởng</a>
                                    </li>
                                </ul>
                            </li>
                            <li >
                                <a href="javascript:;">Thống kê</a>
                                <ul class="dropdown">
                                    <li >
                                        <a href="index.php/statistic/scientist.html">Đội ngũ KHCN</a>
                                    </li>
                                    <li >
                                        <a href="index.php/statistic/topic.html">Nhiệm vụ, đề tài KHCN</a>
                                    </li>
                                    <li >
                                        <a href="index.php/statistic/article.html">Bài báo Khoa học</a>
                                    </li>
                                    <li >
                                        <a href="index.php/statistic/book.html">Sách, giáo trình</a>
                                    </li>
                                    <li >
                                        <a href="index.php/statistic/certificate.html">Sở hữu trí tuệ</a>
                                    </li>
                                    <li >
                                        <a href="index.php/statistic/construction.html">Công trình thực tiễn</a>
                                    </li>
                                    <li >
                                        <a href="index.php/statistic/award.html">Giải thưởng</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="pull-right flip menuzord-menu">
                            <li>
                                <a class="bg-theme-color-2 text-white font-14"
                                   href="index.php/auth/login.html">
                                    Đăng nhập
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- Start main-content -->
    <div class="main-content">

        <!-- Section: home header -->
        <section id="home" class="divider parallax layer-overlay overlay-dark-4"
                 data-bg-img="<?php echo $template_path?>csdl/images/bg5.jpg">
            <div class="display-table">
                <div class="display-table-cell">
                    <div class="container pt-70 pb-70">
                        <div class="row">
                            <div class="col-md-8 pt-50">
                                <div class="home-content sm-text-center">
                                    <h3 class="text-white text-uppercase font-30 font-weight-500 pl-10 mt-0 mb-5 line-height-1">
                                        Cơ Sở Dữ Liệu
                                    </h3>
                                    <h2 class="text-white text-uppercase font-46 font-weight-800 pl-10 mt-0 mb-20 line-height-1">
                                        <span class="text-theme-color-2">Khoa Học</span> Và Công Nghệ
                                    </h2>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="icon-box p-10 pl-0 mb-20">
                                            <a class="icon bg-theme-color-2 icon-circled icon-border-effect effect-circle icon-sm pull-left sm-pull-none flip"
                                               href="index.php/scientist.html">
                                                <i class="pe-7s-pen text-white"></i>
                                            </a>
                                            <div class="ml-70 ml-sm-0">
                                                <a class="text-white font-18" href="index.php/scientist.html">
                                                    DỮ LIỆU NHÀ KHOA HỌC <br/>
                                                    <span class="text-gray line-height-1">2.995
                                                Kết Quả</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="icon-box p-10 pl-0 mb-20">
                                            <a class="icon bg-theme-color-2 icon-circled icon-border-effect effect-circle icon-sm pull-left sm-pull-none flip"
                                               href="index.php/topic.html">
                                                <i class="pe-7s-light text-white"></i>
                                            </a>
                                            <div class="ml-70 ml-sm-0">
                                                <a class="text-white font-18" href="index.php/topic.html">
                                                    DỮ LIỆU ĐỀ TÀI KHOA HỌC<br/>
                                                    <span class="text-gray line-height-1">6.197
                                                Kết Quả</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="icon-box p-10 pl-0 mb-20">
                                            <a class="icon bg-theme-color-2 icon-circled icon-border-effect effect-circle icon-sm pull-left sm-pull-none flip"
                                               href="index.php/article.html">
                                                <i class="pe-7s-phone text-white"></i>
                                            </a>
                                            <div class="ml-70 ml-sm-0">
                                                <a class="text-white font-18" href="index.php/article.html">
                                                    DỮ LIỆU BÀI BÁO KHOA HỌC<br/>
                                                    <span class="text-gray line-height-1">33.240
                                                Kết Quả</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <div class="icon-box p-10 pl-0 mb-20">
                                            <a class="icon bg-theme-color-2 icon-circled icon-border-effect effect-circle icon-sm pull-left sm-pull-none flip"
                                               href="index.php/book.html">
                                                <i class="pe-7s-vector text-white"></i>
                                            </a>
                                            <div class="ml-70 ml-sm-0">
                                                <a class="text-white font-18" href="index.php/book.html">
                                                    DỮ LIỆU SÁCH, GIÁO TRÌNH<br/>
                                                    <span class="text-gray line-height-1">4.407
                                                Kết Quả</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mt-sm-30">
                                <!-- Appointment Form Starts -->
                                <form id="home_appointment_form" name="home_appointment_form"
                                      class="booking-form form-home bg-white p-30" method="post"
                                      action="https://csdlkhoahoc.hueuni.edu.vn/index.php/home/search">
                                    <h3 class="mt-0 mb-20">Tìm Kiếm <span class="text-theme-color-2">Dữ Liệu</span></h3>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group mb-10">
                                                <input id="search_value" name="search_value" class="form-control" type="text"
                                                       placeholder="Nhập từ khóa..."/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group mb-10">
                                                <div class="styled-select">
                                                    <select id="cat_id" name="cat_id" class="form-control">
                                                        <option value="scientist">Nhà khoa học</option>
                                                        <option value="topic">Đề tài khoa học</option>
                                                        <option value="article">Bài báo khoa học</option>
                                                        <option value="book">Sách và giáo trình</option>
                                                        <option value="certificate">Sử hữu trí tuệ</option>
                                                        <option value="construction">Công trình thực tiễn</option>
                                                        <option value="award">Giải thưởng</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group mb-10">
                                                <div class="styled-select">
                                                    <select name="unit_id" id="unit_id" class="form-control">
                                                        <option value="">--Chọn đơn vị--</option>
                                                        <option value="2">Cơ quan Đại học Huế</option>
                                                        <option value="96">---Ban Giám đốc</option>
                                                        <option value="98">---Ban Đào tạo và Công tác sinh viên</option>
                                                        <option value="99">---Ban Thanh tra và Pháp chế</option>
                                                        <option value="102">---Ban Kế hoạch - Tài chính và Cơ sở vật chất</option>
                                                        <option value="104">---Ban Khoa học, Công nghệ và Quan hệ quốc tế</option>
                                                        <option value="108">---Ban Tổ chức cán bộ</option>
                                                        <option value="111">---Văn phòng Đại học Huế</option>
                                                        <option value="112">---Văn phòng Đảng - Đoàn thể Đại học Huế</option>
                                                        <option value="541">---Văn phòng Chương trình VLIR</option>
                                                        <option value="593">---Tạp chí Khoa học</option>
                                                        <option value="582">---Văn phòng Dự án King SeJong - Đại học Huế</option>
                                                        <option value="3">Trường Đại học Khoa học</option>
                                                        <option value="116">---Ban Giám hiệu</option>
                                                        <option value="117">---Phòng Tổ chức - Hành chính</option>
                                                        <option value="120">---Phòng Đào tạo Đại học - Công tác sinh viên</option>
                                                        <option value="124">---Phòng Đào tạo Sau đại học</option>
                                                        <option value="125">---Phòng Kế hoạch tài chính - Cơ sở vật chất</option>
                                                        <option value="129">---Phòng Khảo thí - Đảm bảo chất lượng Giáo dục</option>
                                                        <option value="130">---Phòng Khoa học công nghệ - Hợp tác quốc tế</option>
                                                        <option value="131">---Khoa Báo chí - Truyền thông</option>
                                                        <option value="135">---Khoa Địa lý - Địa chất</option>
                                                        <option value="140">---Khoa Công nghệ thông tin</option>
                                                        <option value="145">---Khoa Hóa học</option>
                                                        <option value="150">---Khoa Kiến trúc</option>
                                                        <option value="154">---Khoa Lịch sử</option>
                                                        <option value="160">---Khoa Lý luận chính trị</option>
                                                        <option value="165">---Khoa Môi trường</option>
                                                        <option value="169">---Khoa Ngữ văn</option>
                                                        <option value="175">---Khoa Sinh học</option>
                                                        <option value="182">---Khoa Toán</option>
                                                        <option value="187">---Khoa Vật lý</option>
                                                        <option value="191">---Khoa Điện tử - Viễn thông</option>
                                                        <option value="192">---Khoa Xã hội học</option>
                                                        <option value="196">---Trung tâm Khoa học xã hội và Nhân văn</option>
                                                        <option value="197">---Trung tâm Nghiên cứu quản lý &amp; Phát triển vùng duyên hải</option>
                                                        <option value="198">---Trung tâm Thông tin - Thư viện</option>
                                                        <option value="202">---Trung tâm Phân tích</option>
                                                        <option value="203">---Trung tâm Tin học</option>
                                                        <option value="204">---Trung tâm tư vấn Kiến trúc và Ứng dụng địa chất</option>
                                                        <option value="543">---Văn phòng Đảng Ủy</option>
                                                        <option value="544">---Văn phòng Công đoàn</option>
                                                        <option value="545">---Văn phòng Đoàn Thanh niên - Hội Sinh viên</option>
                                                        <option value="4">Trường Đại học Sư phạm</option>
                                                        <option value="206">---Ban Giám hiệu</option>
                                                        <option value="207">---Phòng Tổ chức - Hành chính</option>
                                                        <option value="212">---Phòng Đào tạo đại học</option>
                                                        <option value="216">---Phòng Đào tạo Sau đại học</option>
                                                        <option value="217">---Phòng Công tác Sinh viên</option>
                                                        <option value="218">---Phòng Khoa học công nghệ - Hợp tác quốc tế</option>
                                                        <option value="219">---Phòng Kế hoạch - Tài chính</option>
                                                        <option value="222">---Phòng Khảo thí - Đảm bảo chất lượng giáo dục</option>
                                                        <option value="223">---Khoa Địa lý</option>
                                                        <option value="227">---Khoa Tin học</option>
                                                        <option value="232">---Khoa Hóa học</option>
                                                        <option value="238">---Khoa Giáo dục Tiểu học</option>
                                                        <option value="242">---Khoa Lịch sử</option>
                                                        <option value="246">---Khoa Giáo dục Chính trị</option>
                                                        <option value="250">---Khoa Tại chức</option>
                                                        <option value="251">---Khoa Ngữ văn</option>
                                                        <option value="257">---Khoa Sinh học</option>
                                                        <option value="261">---Khoa Toán</option>
                                                        <option value="265">---Khoa Vật lý</option>
                                                        <option value="270">---Khoa Tâm lý - Giáo dục</option>
                                                        <option value="274">---Khoa Sư phạm Kỹ thuật</option>
                                                        <option value="278">---Khoa Giáo dục Mầm non</option>
                                                        <option value="282">---Viện nghiên cứu giáo dục</option>
                                                        <option value="283">---Trung tâm Công nghệ thông tin</option>
                                                        <option value="284">---Trung tâm Thông tin - Thư viện</option>
                                                        <option value="285">---Trung tâm VLLT và Vật lý tính toán</option>
                                                        <option value="546">---Văn phòng Đảng Ủy</option>
                                                        <option value="547">---Văn phòng Công đoàn</option>
                                                        <option value="548">---Văn phòng Đoàn Thanh niên - Hội Sinh viên</option>
                                                        <option value="5">Trường Đại học Nông lâm</option>
                                                        <option value="362">---Ban Giám hiệu</option>
                                                        <option value="363">---Phòng Tổ chức - Hành chính</option>
                                                        <option value="369">---Phòng Đào tạo đại học</option>
                                                        <option value="370">---Phòng Công tác sinh viên</option>
                                                        <option value="371">---Phòng Đào tạo Sau đại học</option>
                                                        <option value="372">---Phòng Kế hoạch - Tài chính</option>
                                                        <option value="373">---Phòng Khảo thí - Đảm bảo chất lượng Giáo dục</option>
                                                        <option value="374">---Phòng Khoa học công nghệ - Hợp tác quốc tế</option>
                                                        <option value="375">---Khoa Nông học</option>
                                                        <option value="383">---Khoa Chăn nuôi - Thú y</option>
                                                        <option value="390">---Khoa Lâm nghiệp</option>
                                                        <option value="396">---Khoa Cơ khí - Công nghệ</option>
                                                        <option value="401">---Khoa Thủy sản</option>
                                                        <option value="406">---Khoa Khuyến nông và Phát triển nông thôn</option>
                                                        <option value="411">---Khoa Tài nguyên đất và Môi trường nông nghiệp</option>
                                                        <option value="415">---Khoa Cơ bản</option>
                                                        <option value="420">---Trung tâm Thông tin - Thư viện</option>
                                                        <option value="424">---Trung tâm NCKH và Phát triển công nghệ Nông Lâm nghiệp</option>
                                                        <option value="425">---Trung tâm Phát triển nông thôn miền Trung</option>
                                                        <option value="426">---Trung tâm Nghiên cứu biến đổi khí hậu miền Trung</option>
                                                        <option value="428">---Viện nghiên cứu và Phát triển</option>
                                                        <option value="552">---Văn phòng Đảng Ủy</option>
                                                        <option value="553">---Văn phòng Công đoàn</option>
                                                        <option value="554">---Văn phòng Đoàn Thanh niên - Hội Sinh viên</option>
                                                        <option value="6">Trường Đại học Y dược</option>
                                                        <option value="287">---Ban Giám hiệu</option>
                                                        <option value="288">---Phòng Tổ chức - Hành chính</option>
                                                        <option value="289">---Phòng Đào tạo đại học - Công tác sinh viên</option>
                                                        <option value="290">---Phòng Đào tạo Sau đại học</option>
                                                        <option value="292">---Phòng Kế hoạch - Tài chính</option>
                                                        <option value="293">---Phòng Quản trị - Cơ sở vật chất</option>
                                                        <option value="294">---Phòng Khảo thí - Đảm bảo chất lượng giáo dục</option>
                                                        <option value="295">---Phòng Khoa học công nghệ - Hợp tác quốc tế</option>
                                                        <option value="296">---Khoa Y tế công cộng</option>
                                                        <option value="297">---Khoa Điều dưỡng</option>
                                                        <option value="298">---Khoa Răng Hàm Mặt</option>
                                                        <option value="299">---Khoa Dược</option>
                                                        <option value="300">---Khoa Cơ bản</option>
                                                        <option value="301">---Bộ môn Nội</option>
                                                        <option value="302">---Bộ môn Ngoại</option>
                                                        <option value="303">---Bộ môn Sản</option>
                                                        <option value="304">---Bộ môn Nhi</option>
                                                        <option value="305">---Bộ môn Ung bướu</option>
                                                        <option value="306">---Bộ môn Gây mê hồi sức</option>
                                                        <option value="307">---Bộ môn Phẩu thuật thực hành</option>
                                                        <option value="308">---Bộ môn Lao</option>
                                                        <option value="309">---Bộ môn Truyền nhiễm</option>
                                                        <option value="310">---Bộ môn Tâm thần</option>
                                                        <option value="311">---Bộ môn Da liễu</option>
                                                        <option value="313">---Bộ môn Chẩn đoán hình ảnh</option>
                                                        <option value="314">---Bộ môn Mắt</option>
                                                        <option value="315">---Bộ môn Tai Mũi Họng</option>
                                                        <option value="316">---Khoa Y học cổ truyền</option>
                                                        <option value="317">---Bộ môn Phục hồi chức năng</option>
                                                        <option value="318">---Bộ môn Sinh lý</option>
                                                        <option value="319">---Bộ môn Di truyền Y học</option>
                                                        <option value="320">---Bộ môn Dược lý</option>
                                                        <option value="321">---Bộ môn Sinh hóa</option>
                                                        <option value="322">---Bộ môn Miễn dịch Sinh lý bệnh</option>
                                                        <option value="323">---Bộ môn Giải phẫu</option>
                                                        <option value="324">---Bộ môn Mô phôi</option>
                                                        <option value="325">---Bộ môn Vi sinh</option>
                                                        <option value="326">---Bộ môn Ký sinh trùng</option>
                                                        <option value="327">---Bộ môn Giải phẩu bệnh - Pháp Y</option>
                                                        <option value="328">---Bộ môn Huyết học</option>
                                                        <option value="329">---Trung tâm Thông tin - Thư viện</option>
                                                        <option value="330">---Bệnh viện trường Đại học Y Dược</option>
                                                        <option value="509">------Ban Giám đốc</option>
                                                        <option value="510">------Phòng kế hoạch tổng hợp</option>
                                                        <option value="511">------Phòng Tài chính - Kế toán</option>
                                                        <option value="512">------Phòng NCKH - đối ngoại</option>
                                                        <option value="513">------Phòng Điều dưỡng</option>
                                                        <option value="514">------Khoa Khám bệnh</option>
                                                        <option value="515">------Khoa Ngoại Tổng hợp</option>
                                                        <option value="516">------Khoa Ngoại Chấn thương, thần kinh, bỏng.</option>
                                                        <option value="517">------Khoa Nội Tổng hợp.</option>
                                                        <option value="518">------Khoa Nội Tim mạch, Hô hấp, xương khớp</option>
                                                        <option value="519">------Khoa Phụ Sản.</option>
                                                        <option value="520">------Khoa Nhi.</option>
                                                        <option value="521">------Khoa Y học cổ truyền.</option>
                                                        <option value="522">------Khoa TMH Mắt RHM</option>
                                                        <option value="523">------Khoa Ung bướu.</option>
                                                        <option value="524">------Khoa Chống nhiễm khuẩn.</option>
                                                        <option value="525">------Khoa Chẩn đoán hình ảnh.</option>
                                                        <option value="526">------Khoa Gây mê - Hồi sức - Cấp cứu</option>
                                                        <option value="527">------Khoa Thăm dò chức năng.</option>
                                                        <option value="528">------Khoa Huyết học - Truyền máu.</option>
                                                        <option value="529">------Khoa Sinh hoá.</option>
                                                        <option value="530">------Khoa Vi sinh.</option>
                                                        <option value="531">------Khoa Ký sinh trùng.</option>
                                                        <option value="532">------Khoa Giải phẫu bệnh.</option>
                                                        <option value="533">------Khoa Dược - Vật tư.</option>
                                                        <option value="534">------Đơn vị tán sỏi.</option>
                                                        <option value="535">------Đơn vị thận nhân tạo.</option>
                                                        <option value="536">------Đơn vị điều trị vô sinh.</option>
                                                        <option value="537">------Đơn vị phẫu thuật mắt bằng máy Laser YAG.</option>
                                                        <option value="538">------Trung tâm Phẫu thuật bằng dao Gamma.</option>
                                                        <option value="549">---Văn phòng Đảng Ủy</option>
                                                        <option value="550">---Văn phòng Công đoàn</option>
                                                        <option value="551">---Văn phòng Đoàn Thanh niên - Hội Sinh viên</option>
                                                        <option value="585">---Tổ Thanh tra Pháp Chế</option>
                                                        <option value="586">---Tổ Công nghệ Thông tin</option>
                                                        <option value="638">---Viện Đào tạo và Bồi dưỡng cán bộ Quản lý y tế</option>
                                                        <option value="639">---Viện Nghiên cứu sức khỏe cộng đồng</option>
                                                        <option value="7">Trường Đại học Kinh tế</option>
                                                        <option value="444">---Ban Giám hiệu</option>
                                                        <option value="445">---Phòng Tổ chức - Hành chính</option>
                                                        <option value="449">---Phòng Đào tạo Đại học - Công tác sinh viên</option>
                                                        <option value="454">---Phòng KHCN - HTQT</option>
                                                        <option value="455">---Phòng Kế hoạch - Tài chính</option>
                                                        <option value="458">---Phòng Khảo thí - Đảm bảo chất lượng Giáo dục</option>
                                                        <option value="461">---Khoa Kinh tế và Phát triển</option>
                                                        <option value="466">---Khoa Quản trị Kinh doanh</option>
                                                        <option value="470">---Khoa Kế toán - Kiểm toán</option>
                                                        <option value="474">---Khoa Kinh tế Chính trị</option>
                                                        <option value="478">---Khoa Hệ thống Thông tin Kinh tế</option>
                                                        <option value="482">---Trung tâm Phát triển doanh nghiệp vừa và nhỏ</option>
                                                        <option value="484">---Trung tâm Đào tạo Quản trị HTX Nông nghiệp</option>
                                                        <option value="485">---Trung tâm Thông tin - Thư viện</option>
                                                        <option value="558">---Văn phòng Đảng Ủy</option>
                                                        <option value="559">---Văn phòng Công đoàn</option>
                                                        <option value="560">---Văn phòng Đoàn Thanh niên - Hội Sinh viên</option>
                                                        <option value="634">---Khoa Tài chính - Ngân hàng</option>
                                                        <option value="8">Trường Đại học Nghệ thuật</option>
                                                        <option value="430">---Ban Giám hiệu</option>
                                                        <option value="431">---Phòng Tổ chức - Hành chính</option>
                                                        <option value="432">---Phòng Khoa học công nghệ - Hợp tác quốc tế</option>
                                                        <option value="433">---Phòng Đào tạo - Công tác sinh viên</option>
                                                        <option value="434">---Tổ Tài vụ</option>
                                                        <option value="435">---Thư viện</option>
                                                        <option value="436">---Khoa Hội họa</option>
                                                        <option value="437">---Khoa Điêu khắc</option>
                                                        <option value="438">---Khoa Sư phạm Mỹ thuật</option>
                                                        <option value="439">---Khoa Mỹ thuật ứng dụng</option>
                                                        <option value="440">---Bộ môn Đồ họa</option>
                                                        <option value="441">---Tổ Cơ sở ngành</option>
                                                        <option value="442">---Tổ Khảo thí</option>
                                                        <option value="555">---Văn phòng Đảng Ủy</option>
                                                        <option value="556">---Văn phòng Công đoàn</option>
                                                        <option value="557">---Văn phòng Đoàn Thanh niên - Hội Sinh viên</option>
                                                        <option value="9">Trường Đại học Ngoại ngữ</option>
                                                        <option value="489">---Ban Giám hiệu</option>
                                                        <option value="490">---Phòng Tổ chức - Hành chính</option>
                                                        <option value="491">---Phòng Đào tạo</option>
                                                        <option value="492">---Phòng Công tác sinh viên</option>
                                                        <option value="493">---Phòng Kế hoạch - Tài chính</option>
                                                        <option value="494">---Phòng Khảo thí - Đảm bảo chất lượng Giáo dục</option>
                                                        <option value="495">---Phòng Khoa học công nghệ - Hợp tác quốc tế</option>
                                                        <option value="496">---Khoa Ngôn ngữ và Văn hóa Nhật Bản</option>
                                                        <option value="497">---Khoa Ngôn ngữ và Văn hóa Hàn Quốc</option>
                                                        <option value="498">---Khoa Quốc tế học</option>
                                                        <option value="499">---Khoa Tiếng Anh chuyên ngành</option>
                                                        <option value="500">---Khoa Tiếng Anh</option>
                                                        <option value="501">---Khoa Tiếng Nga</option>
                                                        <option value="502">---Khoa Tiếng Pháp</option>
                                                        <option value="503">---Khoa Tiếng Trung</option>
                                                        <option value="504">---Trung tâm Thông tin - Thư viện</option>
                                                        <option value="505">---Khoa Việt Nam học</option>
                                                        <option value="562">---Văn phòng Đảng Ủy</option>
                                                        <option value="563">---Văn phòng Công đoàn</option>
                                                        <option value="564">---Văn phòng Đoàn Thanh niên - Hội Sinh viên</option>
                                                        <option value="587">---Trung tâm Nghiên cứu Ứng dụng Ngôn ngữ Văn hóa</option>
                                                        <option value="11">Trường Đại học Luật</option>
                                                        <option value="34">---Ban Giám hiệu</option>
                                                        <option value="35">---Phòng Tổ chức - Hành chính</option>
                                                        <option value="36">---Phòng Đào tạo - Công tác sinh viên</option>
                                                        <option value="37">---Phòng KHCN - HTQT - SĐH</option>
                                                        <option value="38">---Khoa Luật Hành chính</option>
                                                        <option value="39">---Khoa Luật Hình sự</option>
                                                        <option value="40">---Khoa Luật Dân sự</option>
                                                        <option value="41">---Khoa Luật Kinh tế - Quốc tế</option>
                                                        <option value="42">---Trung tâm Tư vấn và Thực hành Pháp luật</option>
                                                        <option value="567">---Đảng ủy Khoa Luật</option>
                                                        <option value="568">---Công đoàn Cơ sở Khoa Luật</option>
                                                        <option value="572">---Văn phòng Đoàn Thanh niên - Hội Sinh viên</option>
                                                        <option value="12">Viện Đào tạo mở và Công nghệ thông tin</option>
                                                        <option value="53">---Ban Giám đốc</option>
                                                        <option value="54">---Phòng Tổ chức - Hành chính</option>
                                                        <option value="58">---Phòng Đào tạo</option>
                                                        <option value="66">---Phòng Kế hoạch - Tài chính</option>
                                                        <option value="69">---Phòng Khảo thí - Đảm bảo chất lượng Giáo dục</option>
                                                        <option value="575">---Đảng Ủy Trung tâm Đào tạo Từ xa</option>
                                                        <option value="577">---Văn Phòng Công đoàn</option>
                                                        <option value="13">Khoa Giáo dục Thể chất</option>
                                                        <option value="611">---Ban Chủ nhiệm Khoa</option>
                                                        <option value="612">---Phòng Tổ chức - Hành chính</option>
                                                        <option value="15">---Bộ môn Điền kinh - Thể dục</option>
                                                        <option value="16">---Bộ môn Bóng</option>
                                                        <option value="569">---Đảng Ủy Khoa GDTC</option>
                                                        <option value="570">---Công đoàn Cơ sở Khoa GDTC</option>
                                                        <option value="573">---Văn phòng Đoàn Thanh niên - Hội Sinh viên</option>
                                                        <option value="601">---Bộ môn Lý luận chuyên ngành</option>
                                                        <option value="613">---Tổ Quản lý Khoa học - Đối ngoại</option>
                                                        <option value="614">---Phòng Đào tạo - Khoa học - Hợp tác quốc tế</option>
                                                        <option value="617">---Bộ môn Cầu lông – Bơi lội</option>
                                                        <option value="619">---Tổ Khảo thí - Đảm bảo chất lượng giáo dục</option>
                                                        <option value="14">Viện Công nghệ Sinh học</option>
                                                        <option value="592">---Ban lãnh đạo Viện</option>
                                                        <option value="596">---Phòng Tổ chức Hành chính</option>
                                                        <option value="628">---Phòng Khoa học, Đào tạo và Hợp tác quốc tế</option>
                                                        <option value="594">---Phòng thí nghiệm Miễn dịch học và Vắc - xin</option>
                                                        <option value="595">---Bộ môn Sinh học và CNSH ứng dụng</option>
                                                        <option value="629">---Phòng thí nghiệm Công nghệ Gen</option>
                                                        <option value="630">---Phòng thí nghiệm Vi sinh vật học  và Công nghệ lên men</option>
                                                        <option value="631">---Phòng thí nghiệm Tế bào</option>
                                                        <option value="632">---Phòng thí nghiệm Công nghệ Enzyme và Protein</option>
                                                        <option value="633">---Trung tâm Chăm sóc và Chữa trị vật nuôi</option>
                                                        <option value="10">---Viện Tài nguyên và Môi trường</option>
                                                        <option value="642">---Trung tâm NC và ƯD kỹ thuật  Y sinh tiên tiến</option>
                                                        <option value="643">---Phòng thí nghiệm Sinh học phân tử</option>
                                                        <option value="17">Trường Du lịch</option>
                                                        <option value="21">---Ban Chủ nhiệm khoa</option>
                                                        <option value="23">---Phòng Giáo vụ - Công tác sinh viên</option>
                                                        <option value="27">---Bộ môn Lữ hành</option>
                                                        <option value="28">---Bộ môn Khách sạn - Nhà hàng</option>
                                                        <option value="29">---Bộ môn CNTT &amp; Truyền thông du lịch dịch vụ</option>
                                                        <option value="30">---Thư viện</option>
                                                        <option value="31">---Trung tâm Thực hành liên kết doanh nghiệp</option>
                                                        <option value="32">---Bộ môn Quản lý sự kiện và Marketing dịch vụ</option>
                                                        <option value="565">---Chi Bộ Khoa Du lịch</option>
                                                        <option value="566">---Công đoàn Cơ sở Khoa Du lịch</option>
                                                        <option value="571">---Văn phòng Đoàn Thanh niên - Hội Sinh viên</option>
                                                        <option value="622">---Phòng Tổ chức - Hành chính</option>
                                                        <option value="624">---Tổ Tài chính - Kế toán</option>
                                                        <option value="625">---Phòng  Khoa học  - Hợp tác quốc tế</option>
                                                        <option value="626">---Bộ môn Du lịch học</option>
                                                        <option value="18">Phân hiệu Đại học Huế tại Quảng trị</option>
                                                        <option value="574">---Chi Ủy Phân hiệu Quảng trị</option>
                                                        <option value="602">---Ban Giám đốc</option>
                                                        <option value="603">---Phòng Tổ chức - Hành chính</option>
                                                        <option value="605">---Bộ môn Công nghệ Kỹ thuật - Môi trường</option>
                                                        <option value="606">---Bộ môn Kỹ thuật trắc địa bản đồ</option>
                                                        <option value="607">---Bộ môn Kỹ thuật Điện</option>
                                                        <option value="608">---Bộ môn Xây dựng dân dụng và Công nghiệp</option>
                                                        <option value="609">---Phòng Đào tạo - Khoa học công nghệ</option>
                                                        <option value="19">Nhà Xuất bản</option>
                                                        <option value="22">Trung tâm Phục vụ Sinh viên</option>
                                                        <option value="86">---Ban Giám đốc</option>
                                                        <option value="87">---Phòng Tài chính - Cơ sở vật chất</option>
                                                        <option value="88">---Tổ Ký túc xá Đội Cung</option>
                                                        <option value="89">---Tổ Ký túc xá Tây Lộc</option>
                                                        <option value="90">---Tổ Ký túc xá Đống Đa</option>
                                                        <option value="91">---Phòng Tổ Chức - Hành Chính</option>
                                                        <option value="92">---Tổ Ký túc xá Trường Bia</option>
                                                        <option value="93">---Tổ Ký túc xá Lưu học sinh nước ngoài</option>
                                                        <option value="25">Trung tâm Giáo dục Quốc phòng và An ninh</option>
                                                        <option value="81">---Ban Giám đốc</option>
                                                        <option value="82">---Phòng Hành chính tổng hợp - Công tác chính trị</option>
                                                        <option value="83">---Phòng Đào tạo - Quản lý sinh viên</option>
                                                        <option value="84">---Khoa Giáo viên</option>
                                                        <option value="576">---Đảng ủy Trung tâm Giáo dục Quốc phòng</option>
                                                        <option value="588">---Văn phòng Công đoàn</option>
                                                        <option value="589">---Văn phòng Đoàn Thanh niên - Hội Sinh viên</option>
                                                        <option value="590">---Bộ phận Khảo thí Đảm bảo Chất lượng</option>
                                                        <option value="637">Khoa Quốc tế</option>
                                                        <option value="640">Khoa Kỹ thuật và Công nghệ</option>
                                                        <option value="641">Trung tâm Khởi nghiệp và Đổi mới sáng tạo</option>
                                                        <option value="51">Cộng tác viên ngoài Đại học Huế</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-0 mt-20">
                                        <input name="form_botcheck" class="form-control" type="hidden" value="">
                                        <button type="submit"
                                                class="btn btn-colored btn-theme-color-2 text-white btn-lg btn-block"
                                                data-loading-text="Please wait...">Tìm Kiếm
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section: About -->
        <section class="">
            <div class="container">
                <div class="section-content">

                    <!-- Section: About -->
                    <section class="">
                        <div class="container">
                            <div class="section-content">
                                <div class="row">
                                    <div class="col-md-6 wow fadeInLeft" data-wow-duration="1s" data-wow-delay="0.3s">
                                        <h2 class="text-uppercase mt-0 font-weight-600">Giới Thiệu Tổng Quan Về<br> <span
                                                    class="text-theme-color-2">Đại Học Huế</span></h2>
                                        <p style="text-align:justify"><span style="color:#ff0000"><span style="font-size:24px"><strong>67</strong></span></span> năm hình thành và phát triển, Đại học Huế ngày nay là một trung tâm đào tạo và nghiên cứu khoa học lớn của miền Trung Việt Nam. Là một trong 14 trường Đại học trọng điểm của cả nước, Đại học Huế đã khẳng định được vị thế, uy tín của mình trong hệ thống giáo dục đại học Việt Nam. Nguồn nhân lực KHCN bao gồm một đội ngũ đông đảo các nhà giáo, nhà nghiên cứu có trình độ cao. Tính cho đến nay Đại học Huế có <strong>4088 </strong>công chức, viên chức và lao động, trong đó có <strong>3050 </strong>công chức, viên chức. Số lượng giảng viên là <strong><strong><a href="index.php/statistic/scientist/type/total.html" target="_blank"><span style="color:#3498db">1831</span></a></strong></strong>, trong đó có <strong><a href="index.php/statistic/scientist/type/quantum.html" target="_blank"><span style="color:#3498db">777</span></a></strong> giảng viên chính và giảng viên cao cấp. Đại học Huế có <strong><a href="index.php/scientist/index/title/3/page/1.html" target="_blank"><span style="color:#3498db">18</span></a></strong> GS, <strong><a href="index.php/scientist/index/title/2/page/1.html" target="_blank"><span style="color:#3498db">181</span></a></strong> PGS; <strong><a href="index.php/scientist/index/degree/3/page/1.html" target="_blank"><span style="color:#3498db">728</span></a></strong> tiến sĩ khoa học, tiến sĩ và <strong><a href="index.php/scientist/index/degree/7/page/1.html" target="_blank"><span style="color:#3498db">14</span></a></strong> chuyên khoa 2; <strong><a href="index.php/scientist/index/degree/2/page/1.html" target="_blank"><span style="color:#3498db">1298</span></a> </strong>thạc sĩ và chuyên khoa 1. Tỷ lệ giảng viên có trình độ sau đại học đạt <strong>87.2%</strong>. Đây là một tài sản quý báu của Đại học Huế, của tỉnh Thừa Thiên Huế và cả nước.</p>

                                        <p style="text-align:justify">Hoạt động đào tạo và NCKH của các nhà giáo, nhà khoa học đã góp phần to lớn và có tính chất quyết định đến sự phát triển của Đại học Huế trong điều kiện hội nhập và mở rộng giao lưu hợp tác quốc tế. Cán bộ giảng viên Đại học Huế đã thực hiện hàng trăm nhiệm vụ NCKH các cấp, đăng tải hàng ngàn bài báo, công trình khoa học trên các Tạp chí trong và ngoài nước, biên soạn hàng trăm giáo trình, sách, tài liệu tham khảo phục vụ thiết thực cho công tác đào tạo và NCKH. Đây là nguồn dữ liệu quý, phục vụ cho việc tra cứu, tham khảo trong đào tạo, đặc biệt là đào tạo Sau đại học và NCKH.</p>

                                        <p style="text-align:justify"><span style="color:#ff0000"><strong>CƠ SỞ DỮ LIỆU KHOA HỌC CÔNG NGHỆ</strong></span> còn là nguồn thông tin giúp cho công tác quản lý trên các lĩnh vực Khoa học Công nghệ, đào tạo Sau đại học, xuất bản TCKH, tư vấn và dịch vụ khoa học,...</p>

                                        <p style="text-align:justify">Chúng tôi trân trọng giới thiệu <span style="color:#ff0000"><strong>"CƠ SỞ DỮ LIỆU KHOA HỌC CÔNG NGHỆ"</strong></span><span style="color:null"> </span>với mong muốn rằng, cùng với các CSDL khác sẽ góp phần phản ánh một cách tương đối đầy đủ nguồn nhân lực chất lượng cao của giáo dục đại học nước nhà.</p>

                                        <p> </p>
                                    </div>
                                    <div class="col-md-6 wow fadeInRight" data-wow-duration="1s" data-wow-delay="0.3s">
                                        <div class="row mb-10">
                                            <div class="col-md-12">
                                                <a href="http://hueuni.edu.vn/" target="_blank" class="hue-uni"><img
                                                            src="<?php echo $template_path?>csdl/images/dhhue.png"></a>
                                                <div class="unis">
                                                    <div class="uni-item-r1">
                                                        <a href="http://hued.hueuni.edu.vn/" target="_blank"><img
                                                                    src="<?php echo $template_path?>csdl/images/logo_supham.png"></a>
                                                        <a href="http://husc.hueuni.edu.vn/" target="_blank"><img
                                                                    src="<?php echo $template_path?>csdl/images/dhkhoahoc.png"></a>
                                                        <a href="http://hce.hueuni.edu.vn/" target="_blank"><img
                                                                    src="<?php echo $template_path?>csdl/images/dhkinhte.png"></a>
                                                        <a href="http://hucfl.hueuni.edu.vn/" target="_blank"><img
                                                                    src="<?php echo $template_path?>csdl/images/dhngoaingu.png"></a>
                                                        <a href="http://fpe.hueuni.edu.vn/" target="_blank"><img
                                                                    src="<?php echo $template_path?>csdl/images/khoathechat.png"></a>
                                                    </div>
                                                    <div class="uni-item-r2">
                                                        <a href="http://hat.hueuni.edu.vn/" target="_blank"><img
                                                                    src="<?php echo $template_path?>csdl/images/khoadulich.png"></a>
                                                        <a href="http://hul.hueuni.edu.vn/" target="_blank"><img
                                                                    src="<?php echo $template_path?>csdl/images/dhluat.png"></a>
                                                        <a href="http://humed.hueuni.edu.vn/" target="_blank"><img
                                                                    src="<?php echo $template_path?>csdl/images/logo_yduoc.png"></a>
                                                        <a href="http://hufa.hueuni.edu.vn/" target="_blank"><img
                                                                    src="<?php echo $template_path?>csdl/images/dhnghethuat.png"></a>
                                                        <a href="http://huaf.hueuni.edu.vn/" target="_blank"><img
                                                                    src="<?php echo $template_path?>csdl/images/dhnonglam.png"></a>
                                                        <a href="http://qtb.hueuni.edu.vn/" target="_blank"><img
                                                                    src="<?php echo $template_path?>csdl/images/phanhieuquangtri.png"></a>
                                                    </div>
                                                    <div class="uni-item-r3">
                                                        <a href="http://huet.hueuni.edu.vn/" target="_blank"><img src="<?php echo $template_path?>csdl/images/logo_huet.png"></a>
                                                        <a href="http://huis.hueuni.edu.vn/" target="_blank"><img src="<?php echo $template_path?>csdl/images/KhoaQuocTe.png"></a>
                                                        <a href="http://huib.hueuni.edu.vn/" target="_blank"><img src="<?php echo $template_path?>csdl/images/vien-CNSH.png"></a>
                                                        <a href="http://iren.hueuni.edu.vn/" target="_blank"><img src="<?php echo $template_path?>csdl/images/vien-TNMT.png"></a>
                                                        <a href="https://hoeit.hueuni.edu.vn/" target="_blank"><img src="<?php echo $template_path?>csdl/images/logo_viendtm.png"></a>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 text-center text-uppercase">
                                                <h3 class="mt-0">
                                                    <small>Trường thành viên, khoa trực thuộc và phân hiệu</small>
                                                    <br/><span class="text-theme-color-2">đại học huế</span></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Section: List News -->
                    <section id="event">
                        <div class="container pb-50">
                            <div class="section-content">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h2 class="line-bottom mt-0 line-height-1">
                                            Tin tức - Thông báo
                                        </h2>
                                        <article class="post media-post clearfix pb-0 mb-10">
                                            <a href="index.php/news/index/code/huong-dan-cap-nhat-thong-tin-ca-nhan-cua-nha-khoa-hoc.html" class="post-thumb mr-20">
                                                <img alt="" src="<?php echo $template_path?>imgs/huongdan.jpg" width="120"/>
                                            </a>
                                            <div class="post-right">
                                                <h4 class="mt-0 mb-5">
                                                    <a href="index.php/news/index/code/huong-dan-cap-nhat-thong-tin-ca-nhan-cua-nha-khoa-hoc.html">Hướng dẫn cập nhật thông tin cá nhân của nhà khoa học</a>
                                                </h4>
                                                <p class="mb-0 font-13">

                                                </p>
                                            </div>
                                        </article>
                                        <article class="post media-post clearfix pb-0 mb-10">
                                            <a href="index.php/news/index/code/huong-dan-su-dung-co-so-du-lieu-khoa-hoc-dai-hoc-hue.html" class="post-thumb mr-20">
                                                <img alt="" src="<?php echo $template_path?>imgs/huongdan.jpg" width="120"/>
                                            </a>
                                            <div class="post-right">
                                                <h4 class="mt-0 mb-5">
                                                    <a href="index.php/news/index/code/huong-dan-su-dung-co-so-du-lieu-khoa-hoc-dai-hoc-hue.html">Hướng dẫn sử dụng Cơ sở dữ liệu khoa học Đại học Huế</a>
                                                </h4>
                                                <p class="mb-0 font-13">

                                                </p>
                                            </div>
                                        </article>
                                        <article class="post media-post clearfix pb-0 mb-10">
                                            <a href="index.php/news/index/code/quy-dinh-quan-ly-va-su-dung-co-so-du-lieu-khoa-hoc-cong-nghe-dai-hoc-hue.html" class="post-thumb mr-20">
                                                <img alt="" src="<?php echo $template_path?>imgs/qd.jpg" width="120"/>
                                            </a>
                                            <div class="post-right">
                                                <h4 class="mt-0 mb-5">
                                                    <a href="index.php/news/index/code/quy-dinh-quan-ly-va-su-dung-co-so-du-lieu-khoa-hoc-cong-nghe-dai-hoc-hue.html">Quy định quản lý và sử dụng Cơ sở dữ liệu khoa học công nghệ Đại học Huế</a>
                                                </h4>
                                                <ul class="list-inline font-12 mb-5">
                                                    <li class="pr-0"><i class="fa fa-calendar mr-5"></i>2018-03-13 14:31:50</li>
                                                </ul>
                                                <p class="mb-0 font-13">

                                                </p>
                                            </div>
                                        </article>
                                        <article class="post media-post clearfix pb-0 mb-10">
                                            <a href="index.php/news/index/code/thong-bao-cap-nhat-du-lieu-khoa-hoc-cong-nghe.html" class="post-thumb mr-20">
                                                <img alt="" src="<?php echo $template_path?>imgs/tb.jpg" width="120"/>
                                            </a>
                                            <div class="post-right">
                                                <h4 class="mt-0 mb-5">
                                                    <a href="index.php/news/index/code/thong-bao-cap-nhat-du-lieu-khoa-hoc-cong-nghe.html">Thông báo cập nhật dữ liệu khoa học công nghệ</a>
                                                </h4>
                                                <ul class="list-inline font-12 mb-5">
                                                    <li class="pr-0"><i class="fa fa-calendar mr-5"></i>2018-03-12 23:31:21</li>
                                                </ul>
                                                <p class="mb-0 font-13">

                                                </p>
                                            </div>
                                        </article>
                                        <div class="clearfix">
                                            <a href="index.php/faq.html">
                                                <img src="<?php echo $template_path?>imgs/faq.png" alt="" class="img-responsive"/>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="http://hueuni.edu.vn/" class="mt-5 btn-block"
                                           target="_blank">
                                            <img class="img-fullwidth" src="<?php echo $template_path?>imgs/DHH_Logo.png" alt="Cổng thông tin Đại học Huế" title="Cổng thông tin Đại học Huế">
                                        </a>
                                        <a href="http://sohuutritue.hueuni.edu.vn/" class="mt-5 btn-block"
                                           target="_blank">
                                            <img class="img-fullwidth" src="<?php echo $template_path?>imgs/SHTT.jpg" alt="Thông tin Sở hữu trí tuệ" title="Thông tin Sở hữu trí tuệ">
                                        </a>
                                        <a href="http://qldh.hueuni.edu.vn/" class="mt-5 btn-block"
                                           target="_blank">
                                            <img class="img-fullwidth" src="<?php echo $template_path?>imgs/ttql.jpg" alt="Trang Quản lý điều hành Đại học Huế" title="Trang Quản lý điều hành Đại học Huế">
                                        </a>
                                        <a href="http://jos.hueuni.edu.vn/" class="mt-5 btn-block"
                                           target="_blank">
                                            <img class="img-fullwidth" src="<?php echo $template_path?>imgs/tckh.jpg" alt="Tạp chí Khoa học Đại học Huế" title="Tạp chí Khoa học Đại học Huế">
                                        </a>
                                        <a href="http://qlkh.hueuni.edu.vn/" class="mt-5 btn-block"
                                           target="_blank">
                                            <img class="img-fullwidth" src="<?php echo $template_path?>imgs/QLDTKH.jpg" alt="Quản lý đề tài khoa học" title="Quản lý đề tài khoa học">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </section>
    </div>

    <!-- Divider: Call To Action -->
    <!--
    <section class="bg-theme-color-2">
        <div class="container pt-10 pb-20">
            <div class="row">
                <div class="call-to-action">
                    <div class="col-md-6">
                        <h3 class="mt-5 text-white font-weight-600">Đăng ký nhận bản tin Đại Học Huế</h3>
                    </div>
                    <div class="col-md-3 text-right flip sm-text-center">
                        <input type="email" class="form-control pull-right" id="exampleInputEmail2"
                               placeholder="Nhập Email của bạn...">
                    </div>
                    <div class="col-md-3 text-left flip sm-text-center">

                        <a class="btn btn-flat btn-theme-colored btn-lg mt-5" href="#">Đăng Ký<i
                                    class="fa fa-angle-double-right font-16 ml-10"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    -->
    <!-- end main-content -->
</div>


<!-- Footer -->
<footer id="footer" class="footer divider layer-overlay overlay-dark-9"
        data-bg-img="<?php echo $template_path?>csdl/images/1920x1280.png">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-md-6">
                <div class="widget dark">
                    <h5 class="widget-title mb-10">Thông tin liên hệ</h5>
                    <ul class="list mt-5">
                        <li class="m-0 pl-10 pr-10">
                            <i class="fa fa-map-marker text-theme-color-2 mr-5"></i>
                            Trường Đại học Công nghệ Sài Gòn. 180 Cao Lỗ, Phường 4, Quận 8, TpHCM.
                        </li>
                        <li class="m-0 pl-10 pr-10">
                            <i class="fa fa-phone text-theme-color-2 mr-5"></i>
                            028 3850 5520
                        </li>
                        <li class="m-0 pl-10 pr-10">
                            <i class="fa fa-user text-theme-color-2 mr-5"></i>
                            Hỗ trợ kỹ thuật: Nguyễn Văn A
                        </li>
                        <li class="m-0 pl-10 pr-10">
                            <i class="fa fa-mobile text-theme-color-2 mr-5"></i>
                            0944555666
                        </li>
                        <li class="m-0 pl-10 pr-10">
                            <i class="fa fa-envelope-o text-theme-color-2 mr-5"></i>
                            contact@stu.edu.vn
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-4 col-md-6 text-center">
                <div class="widget dark">
                    <img class="mt-5" width="200" alt="" src="<?php echo $template_path?>csdl/images/logo-white.png">
                </div>
            </div>
        </div>

    </div>
    <div class="footer-bottom bg-black-333">
        <div class="container pt-20 pb-20">
            <div class="row">
                <div class="col-md-6">
                    <p class="font-11 text-black-777 m-0">Bản quyền thuộc về TRƯỜNG ĐẠI HỌC CÔNG NGHỆ SÀI GÒN © 2024.</p>
                </div>
                <div class="col-md-6 text-right">
                    <div class="widget no-border m-0">
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>


<div id="mydata" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>


<a class="scrollToTop" href="#"><i class="fa fa-angle-up"></i></a>
</div>
<!-- end wrapper -->

<!-- Footer Scripts -->
<script type="text/javascript" src="<?php echo $template_path?>csdl/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $template_path?>assets/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- JS | jquery plugin collection for this theme -->
<script type="text/javascript" src="<?php echo $template_path?>csdl/js/jquery-plugin-collection.js"></script>

<!-- JS | Custom script for all pages -->
<script type="text/javascript" src="<?php echo $template_path?>csdl/js/custom.js"></script>

<!-- Bootstrap notify -->
<script type="text/javascript"
        src="<?php echo $template_path?>assets/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js"></script>
<script type="text/javascript">
    // warning, danger, info
    $(document).ready(function () {
    });
</script>
<script type="text/javascript" src="<?php echo $template_path?>libs/custom.js"></script>

    <jdoc:include type="modules" name="debug" style="none" />
</body>
</html>
