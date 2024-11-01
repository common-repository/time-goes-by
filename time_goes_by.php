<?php
/*
Plugin Name: Time goes by
Plugin URI: https://blog.gti.jp/time-goes-by/
Description: It switches the display by the time the content surrounded by a short code. ショートコードで囲んだコンテンツを時刻等で表示を切り替えるプラグイン
Version: 1.2.9
License: GPL2
Text Domain: time-goes-by
Author: 株式会社ジーティーアイ　さとう　たけし
Author URI: https://gti.co.jp/
 */
/*  Copyright 2017-2024 Takeshi Satoh (https://gti.co.jp/)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require_once 'time_goes_by.class.php';

/** params */
define("TIME_GOES_BY_TIMEZONE", "time_goes_by_timezone");

add_action('admin_menu', 'time_goes_by_admin_menu');

/**
 * Time goes by
 * @param array $atts
 * @param string $content
 * @return string
 */
function shortcode_time_goes_by($atts, $content = null ) {

    // インスタンス生成
    $tgb = new Time_goes_by();

    $atts = shortcode_atts(
    		array(
    				'timezone' => get_option(TIME_GOES_BY_TIMEZONE, $tgb->time_goes_by_get_default_timezone() ),
    				'start_time' => '',
    				'end_time' => '',
    				'config' => '',
    		), $atts );

    // 判定
    $flg = $tgb->judgement( $atts );

    // コンテンツ返却
    return $flg ? do_shortcode( $content ) : "";
}
add_shortcode('tgb', 'shortcode_time_goes_by');

/**
 * 固定ページ等のコンテンツ取得し表示するショートコード
 * @param $atts
 * @return string
 */
function time_goes_by_disp_content( $atts ) {
    $atts = shortcode_atts(
        array(
            'post_id' => '',
            'slug' => '',
            'status' => ''
        ), $atts );
    $post_id = time_goes_by_get_post_id( $atts );
	$status = esc_attr( $atts[ 'status' ] );

	global $post;
	// 投稿内で同じ投稿IDのコンテンツは回帰になるため無視する
	if ( $post_id == $post->ID ) return "";

    if ( !empty( $post_id ) ) {
        $content = get_post( $post_id );
	    ob_start();
	    var_dump( $content );
	    $var = ob_get_contents();
	    ob_end_clean();
	    error_log( $var );
        if ( $content != NULL ) {
            if ( !time_goes_by_is_status( $content->post_status, $status ) ) {
                return "";
            }
            return do_shortcode( $content->post_content );
        }
    }
    return "";
}
add_shortcode( 'disp_content', 'time_goes_by_disp_content' );

/**
 * 固定ページ等のタイトルを取得し表示するショートコード
 * @param array $atts
 * @return string
 */
function time_goes_by_disp_title( $atts ) {
    $atts = shortcode_atts(
        array(
            'post_id' => '',
	        'slug' => '',
            'status' => ''
        ), $atts );
    $post_id = time_goes_by_get_post_id( $atts );
    /** @var array $atts */
	$status = esc_attr( $atts[ 'status' ] );
    if ( !empty( $post_id ) ) {
        $content = get_post( $post_id );
        if ( $content != NULL ) {
            if ( !time_goes_by_is_status( $content->post_status, $status ) ) {
                return "";
            }
            return do_shortcode( $content->post_title );
        }
    }
    return "";
}
add_shortcode( 'disp_title', 'time_goes_by_disp_title' );

/**
 * 固定ページ等のタイトルを取得し表示するショートコード
 * @param array $atts
 * @return string
 */
function time_goes_by_disp_excerpt( $atts ) {
	$atts = shortcode_atts(
		array(
			'post_id' => '',
			'slug' => '',
			'status' => ''
		), $atts );
	$post_id = time_goes_by_get_post_id( $atts );
	$status = esc_attr( $atts[ 'status' ] );
	if ( !empty( $post_id ) ) {
		$content = get_post( $post_id );

		if ( $content != NULL ) {
			if ( !time_goes_by_is_status( $content->post_status, $status ) ) {
				return "";
			}
			return do_shortcode( $content->post_excerpt );
		}
	}
	return "";
}
add_shortcode( 'disp_excerpt', 'time_goes_by_disp_excerpt' );

function time_goes_by_is_status( $content_status, $status = "" ) {
    error_log(" STATUS:".$status);
    error_log("CONTENT_STATUS:".$content_status);
	// post_status を指定できる
	// 公開済 (publish)
	// 予約済 (future)
	// 下書き (draft)
	// 承認待ち (pending)
	// 非公開 (private)
	// ゴミ箱 (trash)
	// 自動保存 (auto-draft)
	// 継承 (inherit)
	if (
		$status == "publish" ||
		$status == "future" ||
		$status == "draft" ||
//                    $status == "pending" ||
		$status == "private" // ||
//                    $status == "trash" ||
//                    $status == "auto-draft" ||
//                    $status == "inherit"

	) {
		if ( $status != $content_status ) {
		    return FALSE;
		}
	}
	return TRUE;
}

/**
 * page_id または slug　から post_id を取得し返す
 * @param $atts
 * @return null
 */
function time_goes_by_get_post_id( $atts ) {
    $post_id = NULL;
	if ( $atts['post_id'] != '' ) {
		$post_id = $atts[ 'post_id' ];
	}
	if ( $atts['slug'] != '' ) {
		$post_id = get_page_by_path( $atts['slug'], "OBJECT", "post" );
		if ( isset( $post_id ) && isset( $post_id->ID ) ) {
			$post_id = $post_id->ID;
		} else {
			$post_id = NULL;
		}
	}
    return $post_id;
}


/**
 * time_goes_by_admin_menu
 * 管理メニューに追加
 */
function time_goes_by_admin_menu() {
    add_options_page('Time goes by', 'Time goes by', 'manage_options', 'time_goes_by.php', 'time_goes_by_setting');
}

/**
 * Param Setting
 * [1] timezone
 */
function time_goes_by_setting() {
	// インスタンス生成
	$tgb = new Time_goes_by();
    // 保存フラグ
	$_saved = FALSE;
	$default_timezone = $tgb->time_goes_by_get_default_timezone();
    if ( isset( $_POST['timezone'] ) ) {
        $bool = date_default_timezone_set( esc_attr( $_POST['timezone'] ) );

        if ( $bool ) {
            $set_timezone = esc_attr( $_POST['timezone'] );
            $_saved = TRUE;
        } else {
            $set_timezone = $default_timezone;
        }
        update_option( TIME_GOES_BY_TIMEZONE, $set_timezone );
    }

    $timezone_param = get_option( TIME_GOES_BY_TIMEZONE, $default_timezone );

	if ( $_saved == TRUE ) { ?>
        <div class="updated" style="padding: 10px; width: 50%;" id="message">Updated success.</div>
    <?php }


    echo <<<EOD
<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2 id="wmpp-title">Time goes By</h2>
    <div style="margin-top: 6px">
        <form action="" method="post">
            <label for="timezone">TIME ZONE: </label> <input name="timezone" value="{$timezone_param}" size="12" /><br />
            <input type="submit" class="button-primary" value="Save" />
        </form>
    </div>
</div>
EOD;
}