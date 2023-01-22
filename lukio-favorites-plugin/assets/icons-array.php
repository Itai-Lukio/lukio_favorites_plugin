<?php

/**
 * return the array of the svg icons available
 * 
 * when adding a new svg pair keep the svg 2 path and classes format, the path need to be set with stroke="rgba(0,0,0,0)".
 * the name need to be added to base.pot manually then update the .om/.po files from the .pot
 * 
 * @return array array(
 *                  array(
 *              'name' => the display name of the icon,
 *              'svg' => svg mark up,
 *              )
 *          )
 */

return array(
    array(
        'name' => __('Heart', 'lukio-favorites-plugin'),
        'svg' => '<svg class="lukio_favorites_button_svg" xmlns="http://www.w3.org/2000/svg" width="18.777" height="17" viewBox="0 0 18.777 17">
            <path class="lukio_pre_fav" d="M536,431.778a4,4,0,0,1,4,4c0,2.209-2.033,3.8-3.555,5.333-1.223,1.23-3.555,3.111-3.555,3.111s-2.332-1.881-3.555-3.111c-1.523-1.532-3.556-3.124-3.556-5.333a4,4,0,0,1,7.111-2.512A3.99,3.99,0,0,1,536,431.778M536,430a5.749,5.749,0,0,0-3.111.908,5.78,5.78,0,0,0-8.889,4.87c0,2.647,1.9,4.477,3.419,5.948.224.216.444.427.654.639,1.277,1.285,3.6,3.163,3.7,3.242a1.778,1.778,0,0,0,2.232,0c.1-.08,2.423-1.957,3.7-3.242.21-.211.43-.423.654-.639,1.523-1.471,3.419-3.3,3.419-5.948A5.784,5.784,0,0,0,536,430Z" transform="translate(-523.5 -429.5)" fill="transparent" stroke="rgba(0,0,0,0)" stroke-miterlimit="10" stroke-width="1" />
            <path class="lukio_fav" xmlns="http://www.w3.org/2000/svg" d="M536,430a5.749,5.749,0,0,0-3.111.908,5.78,5.78,0,0,0-8.889,4.87c0,2.647,1.9,4.477,3.419,5.948.224.216.444.427.654.639,1.277,1.285,3.6,3.163,3.7,3.242a1.778,1.778,0,0,0,2.232,0c.1-.08,2.423-1.957,3.7-3.242.21-.211.43-.423.654-.639,1.523-1.471,3.419-3.3,3.419-5.948A5.784,5.784,0,0,0,536,430Z" transform="translate(-523.5 -429.5)" fill="transparent" stroke="rgba(0,0,0,0)" stroke-miterlimit="10" stroke-width="1" />
            </svg>',
    ),
    array(
        'name' => __('Star', 'lukio-favorites-plugin'),
        'svg' => '<svg class="lukio_favorites_button_svg" viewBox="0 0 1024 129.60001" width="129.60001" version="1.1" sodipodi:docname="star.svg" height="129.60001" inkscape:version="1.2.2 (732a01da63, 2022-12-09)" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg">
            <path class="lukio_pre_fav" d="m 1024.1294,-65.838436 c -8.864,-25.569 -31.61698,-44.289004 -59.00995,-48.353004 0,0 -266.44,-39.618 -266.44,-39.618 0,0 -115.812,-240.455 -115.812,-240.455 -12.192,-25.249 -38.273,-41.41 -66.946,-41.41 -28.673,0 -54.754,16.161 -66.946,41.41 0,0 -115.812,240.455 -115.812,240.455 0,0 -266.472003,39.618 -266.472003,39.618 -27.361,4.064 -50.114,22.784004 -58.9459998,48.353004 -8.801,25.633 -2.144,53.858 17.1839998,73.1219996 0,0 195.271003,194.9509964 195.271003,194.9509964 0,0 -45.282,270.44 -45.282,270.44 -4.608,27.233 7.2,54.562 30.337,70.499 12.704,8.736 27.649,13.184 42.593,13.184 12.289,0 24.609,-3.008 35.778,-8.992 0,0 232.295,-125.06 232.295,-125.06 0,0 232.327,125.06 232.327,125.06 11.169,5.984 23.489,8.992 35.746,8.992 14.944,0 29.888,-4.448 42.625,-13.184 23.137,-15.937 34.881,-43.266 30.305,-70.499 0,0 -45.314,-270.44 -45.314,-270.44 0,0 195.33495,-194.9509964 195.33495,-194.9509964 19.296,-19.2959996 25.92,-47.5209996 17.184,-73.1219996 0,0 0,0 0,0 M 758.74545,151.96056 c -16.385,16.321 -23.809,39.329 -20.065,61.89 0,0 45.314,270.441 45.314,270.441 0,0 -232.328,-124.996 -232.328,-124.996 -11.136,-6.016 -23.425,-8.993 -35.777,-8.993 -12.288,0 -24.609,3.009 -35.745,8.993 0,0 -232.328,124.996 -232.328,124.996 0,0 45.314,-270.441 45.314,-270.441 3.776,-22.561 -3.648,-45.569 -20.033,-61.89 0,0 -195.270001,-194.949996 -195.270001,-194.949996 0,0 266.440001,-39.681 266.440001,-39.681 24.353,-3.617 45.314,-18.849004 55.778,-40.578004 0,0 115.876,-240.391 115.876,-240.391 0,0 115.844,240.423 115.844,240.423 10.496,21.729 31.425,36.929004 55.745,40.578004 0,0 266.505,39.681 266.505,39.681 0,0 -195.27,194.917996 -195.27,194.917996 0,0 0,0 0,0" fill="transparent" stroke="rgba(0,0,0,0)"/>
            <path class="lukio_fav" xmlns="http://www.w3.org/2000/svg" d="m 1024.1294,-65.838436 c -8.864,-25.569 -31.61698,-44.289004 -59.00995,-48.353004 l -266.44,-39.618 -115.812,-240.455 c -12.192,-25.249 -38.273,-41.41 -66.946,-41.41 -28.673,0 -54.754,16.161 -66.946,41.41 l -115.812,240.455 -266.472003,39.618 c -27.361,4.064 -50.114,22.784004 -58.9459998,48.353004 -8.801,25.633 -2.144,53.858 17.1839998,73.1219996 L 220.20045,202.23456 l -45.282,270.44 c -4.608,27.233 7.2,54.562 30.337,70.499 12.704,8.736 27.649,13.184 42.593,13.184 12.289,0 24.609,-3.008 35.778,-8.992 l 232.295,-125.06 232.327,125.06 c 11.169,5.984 23.489,8.992 35.746,8.992 14.944,0 29.888,-4.448 42.625,-13.184 23.137,-15.937 34.881,-43.266 30.305,-70.499 l -45.314,-270.44 L 1006.9454,7.2835636 c 19.296,-19.2959996 25.92,-47.5209996 17.184,-73.1219996 v 0" fill="transparent" stroke="rgba(0,0,0,0)"/>
            </svg>'
    ),
);
