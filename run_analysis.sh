#!/bin/bash
echo "drupal-6.15"
XDEBUG_MODE=off php analyze.php ../../StaticDebloat_Apps/Original/drupal-6.15 ../../StaticDebloat_Apps/LIM_Debloated/drupal-6.15 | tee drupal_615_reduction.txt
echo "drupal-7.34"
XDEBUG_MODE=off php analyze.php ../../StaticDebloat_Apps/Original/drupal-7.34 ../../StaticDebloat_Apps/LIM_Debloated/drupal-7.34 | tee drupal_734_reduction.txt

echo "Joomla_3.4.2"
XDEBUG_MODE=off php analyze.php ../../StaticDebloat_Apps/Original/Joomla_3.4.2 ../../StaticDebloat_Apps/LIM_Debloated/Joomla_3.4.2 | tee joomla_342_reduction.txt
echo "Joomla_3.7.0"
XDEBUG_MODE=off php analyze.php ../../StaticDebloat_Apps/Original/Joomla_3.7.0 ../../StaticDebloat_Apps/LIM_Debloated/Joomla_3.7.0 | tee joomla_370_reduction.txt

echo "PMA 400"
XDEBUG_MODE=off php analyze.php ../../StaticDebloat_Apps/Original/phpMyAdmin-4.0.0-all-languages ../../StaticDebloat_Apps/LIM_Debloated/phpMyAdmin-4.0.0-all-languages | tee pma_400_reduction.txt
echo "PMA 440"
XDEBUG_MODE=off php analyze.php ../../StaticDebloat_Apps/Original/phpMyAdmin-4.4.0-all-languages ../../StaticDebloat_Apps/LIM_Debloated/phpMyAdmin-4.4.0-all-languages | tee pma_440_reduction.txt
echo "PMA 460"
XDEBUG_MODE=off php analyze.php ../../StaticDebloat_Apps/Original/phpMyAdmin-4.6.0-all-languages ../../StaticDebloat_Apps/LIM_Debloated/phpMyAdmin-4.6.0-all-languages | tee pma_460_reduction.txt
echo "PMA 470"
XDEBUG_MODE=off php analyze.php ../../StaticDebloat_Apps/Original/phpMyAdmin-4.7.0-all-languages ../../StaticDebloat_Apps/LIM_Debloated/phpMyAdmin-4.7.0-all-languages | tee pma_470_reduction.txt

echo "WP 46"
XDEBUG_MODE=off php analyze.php ../../StaticDebloat_Apps/Original/WordPress-4.6 ../../StaticDebloat_Apps/LIM_Debloated/WordPress-4.6 | tee wp_460_reduction.txt
echo "WP 471"
XDEBUG_MODE=off php analyze.php ../../StaticDebloat_Apps/Original/WordPress-4.7.1 ../../StaticDebloat_Apps/LIM_Debloated/WordPress-4.7.1 | tee wp_471_reduction.txt
echo "WP 4719"
XDEBUG_MODE=off php analyze.php ../../StaticDebloat_Apps/Original/WordPress-4.7.19 ../../StaticDebloat_Apps/LIM_Debloated/WordPress-4.7.19 | tee wp_4719_reduction.txt
echo "WP 50"
XDEBUG_MODE=off php analyze.php ../../StaticDebloat_Apps/Original/WordPress-5.0 ../../StaticDebloat_Apps/LIM_Debloated/WordPress-5.0 | tee wp_50_reduction.txt


