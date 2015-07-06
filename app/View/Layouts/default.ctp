<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
//$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
    <head>
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php echo $this->fetch('title'); ?>
        </title>

        <?php
        echo $this->Html->meta('icon');

        echo $this->Html->css('style');

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        ?>
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-inverse">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>                        
                        </button>
<!--                        <a class="navbar-brand" href="<?php echo Router::fullBaseUrl(); ?>">Money Lover</a>-->
                    </div>
                    <div class="collapse navbar-collapse" id="myNavbar">
                        <ul class="nav navbar-nav">
                            <?php if (!$this->Session->read('loggedIn')) { ?>
                                <li class="active"><a href="<?php echo Router::fullBaseUrl(); ?>">Home</a></li>
                            <?php } ?>
                            <?php if ($this->Session->read('loggedIn')) { ?>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Categories <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="#">Flower</a></li>
                                        <li><a href="#">Food & Drink</a></li>
                                        <li><a href="#">Sports</a></li>
                                    </ul>
                                </li>
                                <li><a href="#">Wallets</a></li>
                                <li><a href="#">Transaction</a></li>
                                <li><a href="#">Trends</a></li>
                            <?php } ?>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <?php if (!$this->Session->read('loggedIn')) { ?>
                                <li><a href="<?php echo Router::fullBaseUrl() . '/users/register'; ?>"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
                            <?php } else { ?>
                                <li><a href="#"><img /> <?php echo 'Hello, ' . $this->Session->read('name'); ?></a></li>
                                <li><a href="<?php echo Router::fullBaseUrl() . '/users/logOut'; ?>"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <div class="main container">
            <div class="mainContent">
                <div class="content">
                    <?php echo $this->Session->flash(); ?>

                    <?php echo $this->fetch('content'); ?>
                </div>
            </div>
            <footer>
                <?php echo 'Copyright by Aiflytomydr'; ?>
            </footer>
        </div>
</html>
