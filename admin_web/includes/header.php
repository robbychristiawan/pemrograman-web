<?php
// includes/header.php
if (!isset($page_title)) {
    $page_title = "SIMRS Admin Panel";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?= htmlspecialchars($page_title); ?> - SIMRS</title>
    <link rel="icon" href="images/fevicon.png" type="image/png" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="css/responsive.css" />
    <link rel="stylesheet" href="css/colors.css" />
    <link rel="stylesheet" href="css/bootstrap-select.css" />
    <link rel="stylesheet" href="css/perfect-scrollbar.css" />
    <link rel="stylesheet" href="css/custom.css" />
    <link rel="stylesheet" href="css/font-awesome.min.css" />
    <!-- Custom styling to ensure crisp layout and table elements -->
    <style>
        .table-responsive {
            background: #fff;
            border-radius: 6px;
        }
        .white_shd {
            background: #fff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            border-radius: 6px;
            margin-bottom: 30px;
            padding: 25px;
        }
        .full_graph_head h2 {
            font-size: 18px;
            font-weight: 600;
            color: #15283c;
            margin-bottom: 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f1f1;
        }
        .table thead th {
            border-top: none;
            background-color: #f8f9fa;
            color: #212529;
            font-weight: 600;
        }
        .badge-custom {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 4px;
        }
        .main_bt {
            background: #1ed085;
            color: #fff;
            padding: 8px 20px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            display: inline-block;
            font-weight: 500;
            transition: ease all 0.3s;
        }
        .main_bt:hover {
            background: #17ad6e;
            color: #fff;
            text-decoration: none;
        }
        .sidebar_blog_1 {
            background: #15283c;
            padding: 20px 25px;
        }
        .user_info h6 {
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 0;
        }
        .user_info p {
            color: #1ed085;
            font-size: 12px;
            margin-bottom: 0;
        }
        /* Modal overlay styling */
        .modal {
            z-index: 1060 !important;
        }
        .modal-backdrop {
            z-index: 1050 !important;
            opacity: 0.5 !important;
        }
        .modal-content {
            border: none;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .modal-header {
            border-bottom: 1px solid #f0f0f0;
            padding: 18px 24px;
        }
        .modal-body {
            padding: 24px;
        }
        .modal-footer {
            border-top: 1px solid #f0f0f0;
            padding: 16px 24px;
        }
    </style>
</head>
<body class="dashboard dashboard_1">
    <div class="full_container">
        <div class="inner_container">
