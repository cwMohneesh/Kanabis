<?php
	
	if ($trackurl[0] == 'USPS'){
		$urltrack = 'http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum='.$trackno[0];
	} 
	else if ($trackurl[0] == 'AUSTRALIAPOST'){
		$urltrack = 'http://auspost.com.au/track/display.asp?type=article&id='.$trackno[0];	 
	}
	else if ($trackurl[0] == 'AUSTRALIAPOSTINTL'){
		$urltrack = 'http://ice.auspost.com.au/display.asp?ShowFirstScreenOnly=FALSE&ShowFirstRecOnly=TRUE&txtItemNumber='.$trackno[0];
	}
	else if ($trackurl[0] == 'CANADAPOST'){
		$urltrack = 'https://www.canadapost.ca/cpotools/apps/track/personal/findByTrackNumber?trackingNumber='.$trackno[0].'&LOCALE=en';
	}
	else if ($trackurl[0] == 'HKPOST'){
		$urltrack = 'http://app3.hongkongpost.com/CGI/mt/genresult.jsp?tracknbr='.$trackno[0];
	}
	else if ($trackurl[0] == 'ANPOST'){
		$urltrack = 'http://track.anpost.ie/track/track.asp?track='.$trackno[0];
	}
	else if ($trackurl[0] == 'PARCELFORCE'){
		$urltrack = 'http://www.parcelforce.com/track-trace?trackNumber='.$trackno[0].'&page_type=rml-tracking-details';
	}
	else if ($trackurl[0] == 'FEDEX'){
		$urltrack = 'http://www.fedex.com/Tracking?action=track&tracknumbers='.$trackno[0];
	}
	else if ($trackurl[0] == 'DHL'){
		$urltrack = 'http://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB='.$trackno[0];
	}
	else if ($trackurl[0] == 'UPS'){
		$urltrack = 'http://wwwapps.ups.com/WebTracking/processRequest?&tracknum='.$trackno[0];
	}
	else if ($trackurl[0] == 'NZCOURIERS'){
		$track = explode("-", $trackno[0]);
		$urltrack = 'http://www.nzcouriers.co.nz/nzc/servlet/ITNG_TAndTServlet?page=1&VCCA=Enabled&Key_Type=Ticket&product_code='.$track[0].'&serial_number='.$track[1];
	}
	else if ($trackurl[0] == 'POSTNLL'){
		$track = explode("-", $trackno[0]);
		$urltrack = 'https://mijnpakket.postnl.nl/Claim?Barcode='.$track[0].'&Postalcode='.$track[1].'&Foreign=False&ShowAnonymousLayover=False&CustomerServiceClaim=False';
	}
	else if ($trackurl[0] == 'POSTNLINTL'){
		$urltrack = 'https://mijnpakket.postnl.nl/Claim?Barcode='.$trackno[0].'&Postalcode=&Foreign=True&ShowAnonymousLayover=False&CustomerServiceClaim=False';
	}
	else if ($trackurl[0] == 'COURIERPOST'){
		$urltrack = 'http://trackandtrace.courierpost.co.nz/search/'.$trackno[0];
	}
	else if ($trackurl[0] == 'NEWZEALANDPOST'){
		$urltrack = 'http://www.nzpost.co.nz/tools/tracking?trackid='.$trackno[0];
	}
	else if ($trackurl[0] == 'FASTWAY'){
		$urltrack = 'http://fastway.com.au/courier-services/track-your-parcel?l='.$trackno[0];
	}
	else if ($trackurl[0] == 'FASTWAYNZ'){
		$urltrack = 'http://fastway.co.nz/courier-services/track-your-parcel?l='.$trackno[0];
	}
	else if ($trackurl[0] == 'TPCINDIA'){
		$urltrack = 'http://www.tpcindia.com/track.aspx?id='.$trackno[0];
	}
	else if ($trackurl[0] == 'TRADELL'){
		$urltrack = 'http://www.tradelinkinternational.co.in/track.asp?awbno='.$trackno[0];
	}
	else if ($trackurl[0] == 'OMICC'){
		$urltrack = 'http://www.omintl.net/tracking.aspx?AwbNo='.$trackno[0];
	}
	else if ($trackurl[0] == 'ICCW'){
		$urltrack = 'http://www.iccworld.com/track.asp?txtawbno='.$trackno[0];
	}
	else if ($trackurl[0] == 'UACE'){
		$urltrack = 'http://urgentair.co.in/trackshipment_status.php?track='.$trackno[0];
	}
	else if ($trackurl[0] == 'FIRSTFLIGHT'){
		$urltrack = 'http://www.firstflight.net/track.asp?txtcon_no='.$trackno[0];
	}
	else if ($trackurl[0] == 'ORBITWW'){
		$urltrack = 'http://www.orbitexp.com/tools/showTrack.asp?awbnoMul='.$trackno[0];
	}
	else if ($trackurl[0] == 'FLYKING'){
		$urltrack = 'http://www.flykingonline.com/WebFCS/cnotequery.aspx?cnoteno='.$trackno[0];
	}
	else if ($trackurl[0] == 'SHREEMC'){
		$urltrack = 'http://erp.shreemarutionline.com/frmTrackingDetails.aspx?id='.$trackno[0];
	}
	else if ($trackurl[0] == 'SMCS'){
		$urltrack = 'http://www.smcouriers.com/Tracking.aspx?btnchk=A&txtAwb='.$trackno[0];
	}
	else if ($trackurl[0] == 'OVERSEASCS'){
		$urltrack = 'https://webcsw.ocs.co.jp/csw/ECSWG0201R00003P.do?edtAirWayBillNo='.$trackno[0];
	}
	else if ($trackurl[0] == 'BLUEDART'){
		$urltrack = 'http://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=awbquery&awb=awb&numbers='.$trackno[0];
	}
	else if ($trackurl[0] == 'AFLWIZ'){
		$urltrack = 'http://trackntrace.aflwiz.com/Wiz_Summary.jsp?shpntnum='.$trackno[0];
	}
	else if ($trackurl[0] == 'AFLLT'){
		$urltrack = 'http://trackntrace.afllogistics.com/login.do?gcn='.$trackno[0];
	}
	else if ($trackurl[0] == 'BLAZEFLASHD'){
		$urltrack = 'http://www.blazeflash.net/trackdetail.aspx?awbno='.$trackno[0];
	}
	else if ($trackurl[0] == 'BLAZEFLASHI'){
		$urltrack = 'http://www.blazeflash.net/intl/trackfinal.asp?search='.$trackno[0];
	}
	else if ($trackurl[0] == 'ARAMEX'){
		$urltrack = 'http://www.aramex.com/track_results_multiple.aspx?ShipmentNumber='.$trackno[0];
	}
	else if ($trackurl[0] == 'SHREEMAHAC'){
		$urltrack = 'http://www.shreemahavircourier.com/ShipmentDetails.aspx?Type=track&awb='.$trackno[0];
	}
	else if ($trackurl[0] == 'POSTOUK'){
		$urltrack = 'http://www.postoffice.co.uk/track-trace?trackNumber='.$trackno[0].'&page_type=rml-tracking-details';
	}
	else if ($trackurl[0] == 'TNTEXPRESS'){
		$urltrack = 'http://www.tnt.com/webtracker/tracking.do?cons='.$trackno[0].'&trackType=CON&saveCons=Y';
	}
	else if ($trackurl[0] == 'HDNL'){
		$urltrack = 'http://www.hdnl.co.uk/UPI-Tracking-Details/?upi='.$trackno[0];
	}
	else if ($trackurl[0] == 'CITYLINK'){
		$urltrack = 'http://www.city-link.co.uk/dynamic/track.php?parcel_ref_num='.$trackno[0];
	}
	else if ($trackurl[0] == 'JPPOST'){
		$urltrack = 'http://tracking.post.japanpost.jp/service/singleSearch.do?searchKind=S004&locale=en&reqCodeNo1='.$trackno[0].'&x=16&y=15';
	}
	else if ($trackurl[0] == 'POSTDAN'){
		$urltrack = 'http://www.postdanmark.dk/tracktrace/TrackTrace.do?i_lang=INE&i_stregkode='.$trackno[0];
	}
	else if ($trackurl[0] == 'POSTSWEDEN'){
		$urltrack = 'http://www.posten.se/tracktrace/TrackConsignments_do.jsp?trackntraceAction=saveSearch&lang=GB&consignmentId='.$trackno[0];
	}
	else if ($trackurl[0] == 'POSTNORWAY'){
		$urltrack = 'http://sporing.posten.no/sporing.html?q='.$trackno[0].'&lang=en';
	}
	else if ($trackurl[0] == 'PARCEL2GO'){
		$urltrack = 'https://www.parcel2go.com/UniversalTracking.aspx?tk='.$trackno[0];
	}
	else if ($trackurl[0] == 'YODEL'){
		$urltrack = 'http://tracking.yodel.co.uk/wrd/run/wt_pclinf_req_pw.EntryPoint?PCL_NO='.$trackno[0];
	}
	else if ($trackurl[0] == 'COLLECTPLUS'){
		$urltrack = 'https://www.collectplus.co.uk/track/'.$trackno[0];
	}
	else if ($trackurl[0] == 'CITYSPRINT'){
		$urltrack = 'http://ijb.citysprint.co.uk/cs/quiktrak.php?CK=&wwhawb='.$trackno[0];
	}
	else if ($trackurl[0] == 'POSTINDIA'){
		$urltrack = 'http://services.ptcmysore.gov.in/Speednettracking/Track.aspx?articlenumber='.$trackno[0];
	}
	else if ($trackurl[0] == 'INTEXPRESS'){
		$urltrack = 'http://www.interlinkexpress.com/tracking/trackingSearch.do?search.searchType=0&appmode=guest&search.parcelNumber='.$trackno[0];
	}
	else if ($trackurl[0] == 'DPDPARCEL'){
		$urltrack = 'https://tracking.dpd.de/cgi-bin/delistrack?pknr='.$trackno[0].'&typ=1&lang=en';
	}
	else if ($trackurl[0] == 'SPEEDEE'){
		$urltrack = 'http://packages.speedeedelivery.com/packages.asp?tracking='.$trackno[0];
	}
	else if ($trackurl[0] == 'PUROLATOR'){
		$urltrack = 'https://eshiponline.purolator.com/ShipOnline/Public/Track/TrackingDetails.aspx?pup=Y&pin='.$trackno[0];
	}
	else if ($trackurl[0] == 'ONTRAC'){
		$urltrack = 'http://www.ontrac.com/trackingres.asp?tracking_number='.$trackno[0].'&x=16&y=8';
	}
	else if ($trackurl[0] == 'LASERSHIP'){
		$urltrack = 'http://www.lasership.com/track.php?track_number_input='.$trackno[0].'&Submit=Track';
	}
	else if ($trackurl[0] == 'SAFEX'){
		$urltrack = 'http://www.safexpress.com/shipment_inq.aspx?sno='.$trackno[0];
	}
	else if ($trackurl[0] == 'DYNAMEX'){
		$urltrack = 'https://www.dynamex.com/shipping/dxnow-order-track?ctl='.$trackno[0];
	}
	else if ($trackurl[0] == 'ENSENDA'){
		$urltrack = 'http://www.ensenda.com/content/track-shipment?trackingNumber='.$trackno[0].'&TRACKING_SEND=GO';
	}
	else if ($trackurl[0] == 'CEVA'){
		$urltrack = 'http://www.cevalogistics.com/en-US/toolsresources/Pages/CEVATrak.aspx?sv='.$trackno[0];
	}
	else if ($trackurl[0] == 'AONEINT'){
		$urltrack = 'http://www.aoneonline.com/pages/customers/shiptrack.php?tracking_number='.$trackno[0];
	}
	else if ($trackurl[0] == 'PARCELLINK'){
		$urltrack = 'http://www.parcel-link.co.uk/track-and-trace.php?consignment='.$trackno[0];
	}
	else if ($trackurl[0] == 'NAPAREX'){
		$urltrack = 'https://xcel.naparex.com/orders/WebForm/OrderTracking.aspx?OrderTrackingID='.$trackno[0];
	}
	else if ($trackurl[0] == 'PNCOURIER'){
		$urltrack = 'http://www.pos.com.my/emstrack/viewdetail.asp?parcelno='.$trackno[0];
	}
	else if ($trackurl[0] == 'SKYNET'){
		$urltrack = 'http://www.courierworld.com/scripts/webcourier1.dll/TrackingResultwoheader?type=4&nid=1&hawbNoList='.$trackno[0];
	}
	else if ($trackurl[0] == 'GDEX'){
		$urltrack = 'http://203.106.236.200/official/etracking.php?capture='.$trackno[0].'&Submit=Track';
	}
	else if ($trackurl[0] == 'CHRONOS'){
		$urltrack = 'http://chronoscouriers.com/popup/scr_popup_trak_shipment.php?shipmentId='.$trackno[0];
	}
	else if ($trackurl[0] == 'POSMALAY'){
		$urltrack = 'http://www.pos.com.my/emstrack/viewdetail.asp?parcelno='.$trackno[0];
	}
	else if ($trackurl[0] == 'LAPOSTE'){
		$urltrack = 'http://www.csuivi.courrier.laposte.fr/suivi/index/id/'.$trackno[0];
	}
	else if ($trackurl[0] == 'JNEEXP'){
		$urltrack = 'http://www.jne.co.id/index.php?mib=tracking.detail&awb='.$trackno[0];
	}
	else if ($trackurl[0] == 'BRTCE'){
		$urltrack = 'http://as777.brt.it/vas/sped_det_show.hsm?referer=sped_numspe_par.htm&Nspediz='.$trackno[0].'&RicercaNumeroSpedizione=Search';
	}
	else if ($trackurl[0] == 'ROYALMAIL'){
		$urltrack = 'http://www.royalmail.com/portal/rm/track?trackNumber='.$trackno[0];
	}
	else if ($trackurl[0] == 'MYHERMES'){
		$postalcode = str_replace(array('=','=',' '),'',$order->shipping_postcode);
		$urltrack = 'https://www.hermes-europe.co.uk/tracker.html?trackingNumber='.$trackno[0].'&Postcode='.$postalcode;
	}
	else if ($trackurl[0] == 'MYHERMESEU'){
		$urltrack = 'https://www.myhermes.co.uk/tracking-results.html?trackingNumber='.$trackno[0];
	}
	else if ($trackurl[0] == 'SINGPOST'){
		$urltrack = 'http://www.singpost.com/index.php?option=com_tablink&controller=tracking&task=trackdetail&layout=show_detail&tmpl=component&ranumber='.$trackno[0];
	}
	else if ($trackurl[0] == 'GATI'){
		$urltrack = 'http://www.gati.com/single_dkt_track_int.jsp?dktNo='.$trackno[0];
	}
	else if ($trackurl[0] == 'AFGHANPOST'){
		$urltrack = 'http://track.afghanpost.gov.af/index.php?ID='.$trackno[0];
	}
	else if ($trackurl[0] == 'PAKPOST'){
		$urltrack = 'http://ep.gov.pk/track.asp?textfield='.$trackno[0];
	}
	else if ($trackurl[0] == 'LITPOST'){
		$urltrack = 'http://www.post.lt/en/help/parcel-search/index?num='.$trackno[0];
	}
	else if ($trackurl[0] == 'PERUPOST'){
		$urltrack = 'http://clientes.serpost.com.pe/Web-Original/IPSWeb_item_events.asp?itemid='.$trackno[0].'&Submit=Submit';
	}
	else if ($trackurl[0] == 'ROMPOST'){
		$urltrack = 'http://www.posta-romana.ro/en/posta-romana/servicii-online/track-trace.html?track='.$trackno[0];
	}
	else if ($trackurl[0] == 'ELTA'){
		$urltrack = 'http://www.eltacourier.gr/en/webservice_client.php?br='.$trackno[0];
	}
	else if ($trackurl[0] == 'LBCEX'){
		$urltrack = 'http://www.lbcexpress.com/IN/TrackAndTraceResults/0/'.$trackno[0];
	}
	else if ($trackurl[0] == 'PHLPOST'){
		$urltrack = 'http://webtrk1.philpost.org/index.asp?i='.$trackno[0];
	}
	else if ($trackurl[0] == 'APCOVERNIGHT'){
		$track = explode("-", $trackno[0]);
		$urltrack = 'http://www.apc-overnight.com/apc/quickpod.php?txtpostcode='.$track[0].'&txtconno='.$track[1].'&Track=Track&type=1';
	}
	else if ($trackurl[0] == 'UKMAIL'){
		$urltrack = 'https://www.ukmail.com/ConsignmentStatus/UnsecuredConsignmentDetails.aspx?SearchType=Consignment&SearchString='.$trackno[0];
	}
	else if ($trackurl[0] == 'CORREIOS'){
		$urltrack = 'http://websro.correios.com.br/sro_bin/txect01$.Inexistente?P_LINGUA=001&P_TIPO=002&P_COD_LIS='.$trackno[0];
	}
	else if ($trackurl[0] == 'CORREIOSCL'){
		$urltrack = 'http://www.correos.cl/SitePages/seguimiento/seguimiento.aspx?envio='.$trackno[0];
	}
	else if ($trackurl[0] == 'CTT'){
		$urltrack = 'http://www.ctt.pt/feapl_2/app/open/tools.jspx?lang=def&objects='.$trackno[0].'&showResults=true';
	}
	else if ($trackurl[0] == 'SMARTSEND'){
		$urltrack = 'https://www.smartsend.com.au/#!track?consignment='.$trackno[0];
	}
	else if ($trackurl[0] == 'CHRONOEXPRES'){
		$postalcode = str_replace(array('=','=',' '),'',$order->shipping_postcode);
		$urltrack = 'https://www.chronoexpres.com/web/chronoexpres/envios4#https://www.chronoexpres.com/chronoExtraNET/seguimientos/envios/seguimientoPublicoReq.seam?refEnvio='.$trackno[0].'&cpDestEnvio='.$postalcode;
	}
	else if ($trackurl[0] == 'ATSHEALTHCARE'){
		$urltrack = 'http://www.atshealthcare.ca/quickTrackResult.aspx?ship='.$trackno[0];
	}
	else if ($trackurl[0] == 'CANPAR'){
		$postalcode = str_replace(array('=','=',' '),'',$order->shipping_postcode);
		$urltrack = 'http://www.canpar.com/en/track/TrackingAction.do?locale=en&type=2&reference='.$trackno[0].'&shipper_num='.$options['CANPARSCODE'];
	}
	else if ($trackurl[0] == 'COLISSIMO'){
		$urltrack = 'http://www.colissimo.fr/portail_colissimo/suivre.do?language=fr_FR&parcelnumber='.$trackno[0];
	}
	else if ($trackurl[0] == 'CORREOARGENTINO'){
		$urltrack = 'http://www.correoargentino.com.ar/seguimiento_envios/consultar/ondnc/CP/'.$trackno[0].'/AR';
	}
	else if ($trackurl[0] == 'ATSCA'){
		$urltrack = 'http://atssolutions.ca/quickTrackResult.aspx?ship='.$trackno[0];
	}
	else if ($trackurl[0] == 'OCA'){
		$urltrack = 'https://www1.oca.com.ar/OEPTrackingWeb/detalleenviore.asp?numero='.$trackno[0];
	}
	else if ($trackurl[0] == 'SELEKTVRACHT'){
		$urltrack = 'http://www.selektvracht.nl/track-and-trace.shtml?bcode='.$trackno[0];
	}
	else if ($trackurl[0] == 'DHLFORYOU'){
		$urltrack = 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?idc='.$trackno[0];
	}
	else if ($trackurl[0] == 'TCAT'){
		$urltrack = 'http://www.t-cat.com.tw/Inquire/TraceDetail.aspx?BillID='.$trackno[0].'&ReturnUrl=Trace.aspx';
	}
	else if ($trackurl[0] == 'SAPO'){
		$urltrack = 'http://sms.postoffice.co.za/SapoTrackNTrace/TrackNTrace.aspx?id='.$trackno[0];
	}
	else if ($trackurl[0] == 'DPDIE'){
		$urltrack = 'http://www2.dpd.ie/Services/QuickTrack/tabid/222/ConsignmentID/'.$trackno[0].'/Default.aspx';
	}
	else if ($trackurl[0] == 'DHLGER'){
		$urltrack = 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc='.$trackno[0].'&extendedSearch=true';
	}
	else if ($trackurl[0] == 'UPSGER'){
		$urltrack = 'http://wwwapps.ups.com/WebTracking/processRequest?loc=de_DE&tracknum='.$trackno[0];
	}
	else if ($trackurl[0] == 'DANFRA'){
		$urltrack = 'http://tnt.fragt.dk/Servlet/GetData?fbnr='.$trackno[0].'&x=19&y=14';
	}
	else if ($trackurl[0] == 'GLSDEN'){
		$urltrack = 'http://www.gls-group.eu/276-I-PORTAL-WEB/content/GLS/DK01/DA/5004.htm?txtRefNo='.$trackno[0].'&txtAction=71000';
	}
	else if ($trackurl[0] == 'TOLLAU'){
		$urltrack = 'https://online.toll.com.au/trackandtrace/traceConsignments.do?consignments='.$trackno[0];
	}
	else if ($trackurl[0] == 'INTERPARCEL'){
		$urltrack = 'http://www.interparcel.com.au/tracking.php?action=dotrack&trackno='.$trackno[0];
	}
	else if ($trackurl[0] == 'DTDC'){
		$urltrack = 'http://dtdc.com/tracking/tracking_results.asp?action=track&sec=tr&ctlActiveVal=1&Ttype=awb_no&GES=N&strCnno='.$trackno[0];
	}
	else if ($trackurl[0] == 'AUSTRIAPOST'){
		$urltrack = 'http://www.post.at/en/track_trace.php?pnum1='.$trackno[0];
	}
	else if ($trackurl[0] == 'HAYPOST'){
		$urltrack = 'http://www.haypost.am/view-lang-eng-getemsdata-page.html?itemid='.$trackno[0];
	}
	else if ($trackurl[0] == 'BELARUSPOST'){
		$urltrack = 'http://search.belpost.by/#'.$trackno[0];
	}
	else if ($trackurl[0] == 'BELGIUMPOST'){
		$urltrack = 'http://track.bpost.be/etr/light/performSearch.do?itemCodes='.$trackno[0];
	}
	else if ($trackurl[0] == 'BULGARIANPOST'){
		$urltrack = 'http://www.bgpost.bg/IPSWebTracking/IPSWeb_item_events.asp?itemid='.$trackno[0];
	}
	else if ($trackurl[0] == 'CZECHPOST'){
		$urltrack = 'http://www.ceskaposta.cz/en/nastroje/sledovani-zasilky.php?go=ok&barcode='.$trackno[0];
	}
	else if ($trackurl[0] == 'FINLANDPOST'){
		$urltrack = 'http://www.posti.fi/itemtracking/posti/search_by_shipment_id?ShipmentId='.$trackno[0];
	}
	else if ($trackurl[0] == 'CHRONOPOSTFR'){
		$urltrack = 'http://www.chronopost.fr/expedier/inputLTNumbersNoJahia.do?chronoNumbers='.$trackno[0];
	}
	else if ($trackurl[0] == 'TEMANDO'){
		$urltrack = 'https://www.temando.com/education-centre/support/track-your-item?token='.$trackno[0];
	}
	else if ($trackurl[0] == 'SEUR'){
		$urltrack = 'http://www.seur.com/es/seguimiento-online.do?segOnlineIdentificador='.$trackno[0];
	}
	else if ($trackurl[0] == 'CHINAPOST'){
		$urltrack = 'http://intmail.183.com.cn/';
	}
	else if ($trackurl[0] == 'DEUTSCHEPOST'){
		$urltrack = 'https://www.deutschepost.de/sendung/simpleQuery.html?locale=en_GB';
	}
	else if ($trackurl[0] == 'PBTNZ'){
		$urltrack = 'http://www.pbt.co.nz/default.aspx';
	}
	else if ($trackurl[0] == 'THAIPOST'){
		$urltrack = 'http://track.thailandpost.co.th/trackinternet/Default.aspx';
	}
	else if ($trackurl[0] == 'POSTEIT'){
		$urltrack = 'http://www.poste.it/online/dovequando/home.do';
	}
	else if ($trackurl[0] == 'ISRAELPOST'){
		$urltrack = 'http://www.israelpost.co.il/itemtrace.nsf/mainsearch';
	}
	else if ($trackurl[0] == 'RUPOST'){
		$urltrack = 'http://www.russianpost.ru/rp/servise/en/home/postuslug/trackingpo';
	}
	else if ($trackurl[0] == 'TRACKON'){
		$urltrack = 'http://www.trackoncouriers.com/';
	}
	else if ($trackurl[0] == 'POSINDO'){
		$form = 'yes';
		$urltrack = '<form method="POST" name="'.$trackurl[0].'" target="_blank" action="http://www.posindonesia.co.id/add-ons/lacak-kiriman/lacakk1121m4np05.php" ><input name="q" type="hidden" value="'.$trackno[0].'" /><input type="hidden" name="jenis" value="0" /></form>';
	}