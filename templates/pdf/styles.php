<style type="text/css">
	* { -webkit-print-color-adjust: exact; }

	/*
        Use the DejaVu Sans font for display and embedding in the PDF file.
        The standard PDF fonts have no support for Unicode characters.
    */
    .pdf-page {
        font-family: "Arial", Helvetica, sans-serif;
        font-size: 12px;
        margin: 0 auto;
        box-sizing: border-box;
        box-shadow: 0 3px 10px 0 rgba(0,0,0,.3);
        background-color: #fff;
        color: #333;
        position: relative;
    }
    .size-a4 { 
    	width: 800px;
    	min-height: 11in;
    	padding: 30px 50px;
    }

    .button {
    	display: inline-block;
		padding: 6px 12px;
		margin-bottom: 0;
		font-size: 14px;
		font-weight: 400;
		line-height: 1.42857143;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
		-ms-touch-action: manipulation;
		touch-action: manipulation;
		cursor: pointer;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
		background-image: none;
		border: 1px solid transparent;
		border-radius: 4px;
		color: #333;
	    background-color: #e6e6e6;
	    border-color: #adadad;
	    margin-right: 20px;
	    margin-bottom: 20px;
    }

    a {
    	color: #333;
    }

    h1, h2 {
    	line-height: 1.5em;
		font-weight: bold;
		margin: 0;
    }
	h1 {
		font-size: 18px;
		text-transform: uppercase;
	}
	h2 {
		font-size: 20px;
		text-align: right;
	}

	p {
		font-size: 14px;
	    line-height: 1.4em;
	    margin: 0;
	}

	.left {
		float: left;
	}
	.align-left {
		text-align: left;
	}
	.right {
		float: right;
	}
	.align-right {
		text-align: right;
	}
	.align-center {
		text-align: center;
	}
	.center {
		float: none;
		margin: 0 auto;
		text-align: center;
		width: 100%;
	}
	.clear {
		clear: both;
	}
	.container {
		background: #FFF;
		margin: 1em auto;
		padding: 2em;
	}

	/* ============= *
	 * ORDER DETAILS *
	 * ============= */

	.company-logo {
		padding-right: 20px;
	}
	.company-logo img {
		max-width: 100px;
	}
	.company-logo a {
		display: inline-block;
	}
	.company-contacts address {
	    font-style: normal;
	    line-height: 1.2em;
	    margin-bottom: 15px;
	}
	.company-contacts a {
		text-decoration: none;
	}

	.quote {
		width: 205px;
	}

	.quote table {
		margin-top: 5px;
		margin-bottom: 0;
		text-align: center;
	}
	.quote table td {
		padding: 7px;
	}

	.quote table tr:first-child td:first-child {
		border-right-width: 0;
	}
	.quote table tr:first-child td:first-child,
	.quote table tr:first-child td:last-child {
		border-bottom-width: 0;
	}

	.quote table tr:last-child td:first-child {
		border-right-width: 0;
	}

	.document-user-info .quote-for {
		max-width: 330px;
		margin: 0; 
	}
	.document-user-info .quote-for tr:first-child td {
		border-bottom-width: 0;
	}
	.document-user-info .quote-for td p {
		font-size: 12px;
	}

	.document-user-info .quote-valid {
		max-width: 250px;
		margin: 0;
	}
	.document-user-info .quote-valid td {
		padding: 20px 5px;
	    font-size: 16px;
	    text-align: center;
	    font-weight: bold;
	    line-height: 1.4em;	
	}
	.company-information {
		width: 490px;
		margin-bottom: 4em;
	}


	/* ============ *
	 * TABLES *
	 * ============ */

	table {
		border-collapse: collapse;
		margin: 4em 0 2em;
		text-align: left;
		width: 100%;
	}
	table td,
	table th {
		border: 1px solid #333;
		font-weight: normal;
		padding: 0.8em 1.2em;
		text-transform: none;
		vertical-align: top;
		page-break-inside: avoid;
	}
	table th {
		-webkit-print-color-adjust: exact;
	}
	.invoice-body tbody tr:nth-child(even) {
		background-color: #eee;
	}
	.invoice-body tbody tr:nth-child(odd) {
		background-color: #FFFFFF;
	}

	.invoice-table thead tr {
		background-color: #eee;
	}
	.invoice-table th {
		text-align: center;
		padding: 5px;
		font-weight: normal;
	}
	.invoice-table td {
		padding: 2px 5px;
	}
	.invoice-table thead .quantity 		{ width: 5%; }
	.invoice-table thead .item-number { 
		width: 20%;
		border-left-width: 0;
	}
	.invoice-table thead .description {
		width: 45%;
		border-left-width: 0;
	}
	.invoice-table thead .unit-price {
		width: 15%;
		border-left-width: 0;
	}
	.invoice-table thead .total {
		width: 15%;
		border-left-width: 0;
	}

	.invoice-table tbody td {
		border-top: 0;
		border-bottom: 0;
	}

	.invoice-table tbody tr td:not(:first-child) {
    	border-left-width: 0;
	}

	.invoice-table tbody .quantity,
	.invoice-table tbody .unit-price,
	.invoice-table tbody .total,
	.invoice-table tbody .percent,
	.invoice-table tbody .discount-total {
		text-align: right;
	}

	.invoice-table tbody .percent,
	.invoice-table tbody .discount-total {
		padding-right: 10px;
	}

	.invoice-table tfoot td.total,
	.invoice-table tfoot td.value {
		font-size: 16px;
		padding: 5px 10px;
		font-weight: bold;
	}
	.invoice-table tfoot td.value {
		font-size: 15px;
		text-align: right;
		border-left-width: 0;
	}

	.alert {
		display: none;
		position: relative;
    	top: -8px;
    	padding: 8px 15px;
    	border: 1px solid transparent;
    	border-radius: 4px;
	}
	.alert-success {
		color: #3c763d;
	    background-color: #dff0d8;
	    border-color: #d6e9c6;	
	}
    
    
    

	/* ============ *
	 * PRINT STYLES *
	 * ============ */

	@media print {

		/* Background is always white in print */
		html, body {
			background: #FFFFFF;
		}
		a {
			text-decoration: none;
		}
		.pdf-page {
			box-shadow: none;
		}
		.action-box {
			display: none;
		}
		table {
			page-break-inside: auto;
		}
		table tr {
			page-break-inside: avoid;
			page-break-after: auto;
		}
		table td,
		table th {
			padding: 0.4em 1.2em;
			page-break-inside: avoid;
			page-break-after: auto;
		}
	}
</style>