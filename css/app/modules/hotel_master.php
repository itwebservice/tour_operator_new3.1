<?php 
header("Content-type: text/css");
?>
/*=================Booking css start==================*/
#tbl_hotel_booking input[type="text"], 
#tbl_hotel_booking_update input[type="text"],
#tbl_hotel_booking select, 
#tbl_hotel_booking_update select,
#tbl_hotel_booking textarea, 
#tbl_hotel_booking_update textarea{
	height: 40px;
	font-size: 12px;
}
#tbl_hotel_booking select[name="received_documents"], 
#tbl_hotel_booking_update select[name="received_documents"]{
	height: 50px;
}
#tbl_hotel_booking td, 
#tbl_hotel_booking_update td{
    padding: 8px 4px;
}
#tbl_hotel_booking td, 
#tbl_hotel_booking_update td{
    padding: 8px 4px;
}
#booking_save_modal #hotel_booking_wrap .select2-container,
#booking_update_modal #hotel_booking_wrap .select2-container {
    z-index: 1;
}
#booking_save_modal .select2-container--open,
#booking_update_modal .select2-container--open {
    z-index: 10060 !important;
}
#booking_save_modal .select2-dropdown,
#booking_update_modal .select2-dropdown {
    z-index: 10060 !important;
}
/*=================Booking css end==================*/