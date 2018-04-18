<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Insert title here</title>
</head>
<body>
<h1>This is Sale TY Page</h1>
</body>

<!-- GL MAIN -->
<script type="text/javascript">
var _gl_client = '50'; // don't change
var _gl_page_type = 'sales_thankyou'; // page type one of (salesletter, optin, optin_thankyou, sales_thankyou, upsell, blog, content, order, landing, checkout)
var _gl_labels = []; // array of labels  
var _gl_optin = null; // put email value if this is optin track 
var _gl_amount = '<?=rawurldecode($_GET['amount'])?>'; // put amount of product if this is sale track
var _gl_redirect_url = null; // put url to redirect to after track
var _gl_dedup_sales = true; // turn on sales deduplication algorithm. Value can be true or false.
var _gl_sale_code = '<?=rawurldecode($_GET['sale_code'])?>'; // this field will be used to distinguish different orders inside of deduplication algorithm
var _gl_product_code = '<?=rawurldecode($_GET['product_code'])?>'; // name or code of the product

(function(){var t=document.createElement('script');t.type='text/javascript';t.async=true;t.src="//img.ips.ms/gl.js";var s=document.getElementsByTagName('script')[0];s.parentNode.appendChild(t,s);})();   
</script>
<noscript><img style="display: none;" src="//img.ips.ms/50-abc123.gif?c=50&pt=landing"></noscript>

<!-- GLBKP -->
<img src="http://cdn-ips.com/50-abc123.gif?c=50" style="position:absolute; visibility:hidden">

<h3>Thanks for purchase <?=print_r($_GET, true)?> </h3>
</html>