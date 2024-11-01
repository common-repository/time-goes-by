<?php
/**
 * Time goes by
 * class
 * @version 1.2.9
 * @since 1.2
 */

class Time_goes_by {

	public function __constructor() {

	}

	/**
	 * Judgement Method
	 * @param array $values
	 * @return bool
	 */
	public function judgement( $values = NULL ) {
		if ( $values == NULL ) return NULL;

		$this->set_timezone( $values['timezone'] );

		$now_time = $this->time_goes_by_get_strtotime( date( 'YmdHis' ), $values['timezone'] );

		$flg = TRUE;

		if ( $values['start_time'] != "" && strlen( trim( $values['end_time'] ) ) == 0 ) {

			if ( $now_time >= $this->time_goes_by_get_strtotime( $values['start_time'], $values['timezone'] ) ) {
				// N/A
			} else {
				$flg = FALSE;
			}
		} else if ( trim( $values['start_time'] ) != "" && trim( $values['end_time'] ) != "" ) {

			if ( $this->time_goes_by_get_strtotime( $values['start_time'], $values['timezone'] ) <= $now_time &&
					$this->time_goes_by_get_strtotime( $values['end_time'], $values['timezone'] ) > $now_time ) {
				// N/A
			} else {
				$flg = FALSE;
			}
		} else if ( $values['end_time'] != "" && strlen( trim( $values['start_time'] ) ) == 0 ) {

			if ( $this->time_goes_by_get_strtotime( $values['end_time'], $values['timezone'] ) > $now_time ) {
				// N/A
			} else {
				$flg = FALSE;
			}
		}

		$configs = array();
		if ( $flg == TRUE ) {
			// configパラメータの判定
			if ( $values['config'] != "" ) {
				// config 分解
				$config_array = explode( ",", $values['config'] );
				// 分解結果を順番に判定する（解せないところは無視する）
				if ( count( $config_array ) > 0 ) {
					foreach ( $config_array as $conf_item ) {
						$conf_item_sub = trim( substr( $conf_item, 0, strpos( $conf_item, ":" ) ) );
						$conf_item_body = trim( substr( $conf_item, strpos( $conf_item, ":" ) + 1 ) );
						$configs[$conf_item_sub] = $conf_item_body;
						switch ( $conf_item_sub ) {
							case "hour":
							case "hours":
								$flg = $this->judgement_hour( $conf_item_body );
								if ( !$flg ) return FALSE;
								break;
							case "week":
							case "weeks":
								$flg = $this->judgement_week( $conf_item_body );
								if ( !$flg ) return FALSE;
								break;
							case "day":
							case "days":
								$flg = $this->judgement_days( $conf_item_body );
								if ( !$flg ) return FALSE;
								break;
							case "month":
								$flg = $this->judgement_month( $conf_item_body );
								if ( !$flg ) return FALSE;
								break;
							case "last_day_month":
								$flg = $this->judgement_last_day_month( $conf_item_body );
								if ( !$flg ) return FALSE;
								break;
						}
					}
				}
			}
		}
		// 任意の処理を導入する
		$flg = apply_filters( 'time_goes_by_judgement', $flg, $configs, $values );
		return $flg;
	}

	/**
	 * Set Timezone
	 * @param string $timezone
	 */
	function set_timezone( $timezone = NULL ) {
		if ( $timezone == NULL ) $timezone = get_option( TIME_GOES_BY_TIMEZONE, $this->time_goes_by_get_default_timezone() );
		date_default_timezone_set( $timezone );
	}

	/**
	 * デフォルトタイムゾーン取得（WordPress用）
	 */
	function time_goes_by_get_default_timezone() {
		$default_timezone = wp_timezone_string();
		// タイムゾーンがGMT_OFFSETで数字が入っている場合（例: +09:00 は、Etc/GMT-9に -09:00 は、Etc/GMT+9に変換する）
		if ( preg_match( "/^([+-])([0-9]{2}):([0-9]{2})$/", $default_timezone, $matches ) ) {
			$mark = $matches[1];
			// $mark 反転
			if ( $mark == "+" ) {
				$mark = "-";
			} else {
				$mark = "+";
			}
			$hour = intval($matches[2]);
			$default_timezone = "Etc/GMT" . $mark . $hour;
		}
		return $default_timezone;
	}

	/**
	 * time_goes_by_get_strtotime
	 * @param string $str YmdHis等date型のフォーマット文字列
	 * @param string $timezone Timezone文字列
	 * @return false|int
	 */
	function time_goes_by_get_strtotime( $str, $timezone = NULL ) {
		$this->set_timezone( $timezone );
		$ts = strtotime( $str );
		return $ts;
	}

	/**
	 * Judgement hour
	 * @param string $str
	 * @return boolean
	 */
	function judgement_hour( $str ) {
		$params = $this->separater( $str );
		$this->set_timezone();
		$current_hour = strtoupper( date('H') );
		$flg = FALSE;
		foreach ( $params as $item ) {
			if ( !is_numeric( $item ) ) {
				continue;
			}
			if ( $current_hour == $item ) {
				return TRUE;
			}
		}
		return $flg;
	}

	/**
	 * separater
	 * @param $val
	 * @return array|null
	 */
	function separater( $val ) {
		$ret_ary = NULL;
		if ( strpos($val, "|") > 0 ) {
			$ret_ary = preg_split( "/\|+/i", $val );
		}
		if ( stripos($val , " or " ) > 0 ) {
			$ret_ary = preg_split( "/ or /i", $val );
		}
		if ( $ret_ary == NULL ) {
			return array( $val );
		}
		return $ret_ary;
	}

	/**
	 * Judgement Week
	 * @param string $str
	 * @return boolean
	 */
	function judgement_week( $str ) {
		$params = $this->separater( $str );

		$timezone = get_option(TIME_GOES_BY_TIMEZONE, $this->time_goes_by_get_default_timezone());
		date_default_timezone_set($timezone);

		$current_week = strtoupper( date('D') );
		echo "<!--". date('Y-m-d H:i:s D') ."-->";
		// timezone表示
		echo "<!--". date_default_timezone_get() ."-->";
		$flg = FALSE;
		foreach ( $params as $item ) {
			$test_week = strtoupper( $this->convert_week( $item ) );
			if ( $current_week == $test_week ) {
				return TRUE;
			}
		}
		return $flg;
	}

	/**
	 *
	 * @param string $w_str 日本語曜日文字列
	 * @return string 英語曜日文字列
	 */
	function convert_week( $w_str ) {
		$ret = $w_str;
		switch ( $w_str ) {
			case "日":
				$ret = "SUN";
				break;
			case "月":
				$ret = "MON";
				break;
			case "火":
				$ret = "TUE";
				break;
			case "水":
				$ret = "WED";
				break;
			case "木":
				$ret = "THU";
				break;
			case "金":
				$ret = "FRI";
				break;
			case "土":
				$ret = "SAT";
				break;
		}
		return $ret;
	}

	/**
	 * Judgement Days
	 * @param string $str
	 * @return boolean
	 */
	function judgement_days( $str ) {
		$params = $this->separater( $str );
		$this->set_timezone();
		$current_day = date('j');
		$flg = FALSE;
		foreach ( $params as $item ) {
			if ( $current_day == $item ) {
				return TRUE;
			}
		}
		return $flg;
	}

	/**
	 * Judgement Month
	 * @param string $str
	 * @return boolean
	 */
	function judgement_month( $str ) {
		$params = $this->separater( $str );
		$this->set_timezone();
		$current_month = date('n');

		foreach ( $params as $item ) {
			if ( $current_month == $item ) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Judgement Last Day of Month
	 * @param string $str
	 * @return boolean
	 */
	function judgement_last_day_month( $str ) {
		$params = $this->separater( $str );
		$target_lastdays = intval( $params[0] );
		$this->set_timezone();
		$lastday = date('t');
		$today = date('j');

		if ( $today > $lastday - $target_lastdays ) {
			return TRUE;
		}
		return FALSE;
	}
}