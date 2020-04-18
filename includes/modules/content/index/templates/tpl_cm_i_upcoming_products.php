<div class="col-sm-<?php echo $content_width; ?> cm-i-upcoming-products">
  <table class="table table-striped table-sm">
    <tbody>
      <tr>
        <th><?php echo MODULE_CONTENT_UPCOMING_PRODUCTS_TABLE_HEADING_PRODUCTS; ?></th>
        <th class="text-right"><?php echo MODULE_CONTENT_UPCOMING_PRODUCTS_TABLE_HEADING_DATE_EXPECTED; ?></th>
      </tr>
      <?php
		//echo '<pre>';print_r($l_Products->getData());echo '</pre>';
		//exit();
		echo '<h2>here</h2>';
		foreach($l_Products->getData() as $l_product)
		{
			echo '<tr>';
			echo '  <td><a href="' . tep_href_link('product_info.php', 'products_id=' . $l_product->getID()) . '">' . $l_product->getTitle() . '</a></td>';
        echo '  <td class="text-right">' . tep_date_short($l_product->getDateAvailable()) . '</td>';
        echo '</tr>'; 
		}
	/*	foreach($l_Products->getData() as $expected) {
			var_dump($expected);
			echo '<tr>';
			echo '  <td><a href="' . tep_href_link('product_info.php', 'products_id=' . (int)$expected['products_id']) . '">' . $expected['products_name'] . '</a></td>';
        echo '  <td class="text-right">' . tep_date_short($expected['date_available']) . '</td>';
        echo '</tr>'; 
		}*/
      ?>
    </tbody>
  </table>
</div>

<?php
/*
  Copyright (c) 2018, G Burton
  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
?>
        