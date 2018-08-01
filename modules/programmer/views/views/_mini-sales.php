<?php

if ($sales) {
    foreach ($sales as $sale) {
        echo $this->render('_mini-sale', compact(['sale','controls', 'salefilter', 'contacts']));
    }
}
