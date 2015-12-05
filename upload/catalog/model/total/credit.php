<?php
// *	@copyright	OPENCART.PRO 2011 - 2015.
// *	@forum	http://forum.opencart.pro
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelTotalCredit extends Model {
	public function getTotal($totals) {
		extract($totals);
		
		$this->load->language('total/credit');

		$balance = $this->customer->getBalance();

		if ((float)$balance) {
			if ($balance > $total) {
				$credit = $total;
			} else {
				$credit = $balance;
			}

			if ($credit > 0) {
				$total_data[] = array(
					'code'       => 'credit',
					'title'      => $this->language->get('text_credit'),
					'value'      => -$credit,
					'sort_order' => $this->config->get('credit_sort_order')
				);

				$total -= $credit;
			}
		}
	}

	public function confirm($order_info, $order_total) {
		$this->load->language('total/credit');

		if ($order_info['customer_id']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$order_info['customer_id'] . "', order_id = '" . (int)$order_info['order_id'] . "', description = '" . $this->db->escape(sprintf($this->language->get('text_order_id'), (int)$order_info['order_id'])) . "', amount = '" . (float)$order_total['value'] . "', date_added = NOW()");
		}
	}

	public function unconfirm($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
	}
}