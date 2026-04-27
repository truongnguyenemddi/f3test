function RestrictCharSp(str){
			var arr = ["$","%","^","&","*","'","\"",":"];
			var error = 0 ;
			jQuery.each(arr, function(i,val) { 								
				((str.indexOf(val)<0) ? {} : error++);				
			});
			return error ;
		} 	  
	  
	 $.validator.addMethod("CheckVNdate", function (value, element, params) {
            function GetFullYear(year, params) {
                var twoDigitCutoffYear = params.cutoffyear % 100;
                var cutoffYearCentury = params.cutoffyear - twoDigitCutoffYear;
                return ((year > twoDigitCutoffYear) ? (cutoffYearCentury - 100 + year) : (cutoffYearCentury + year));
            }
            if (value.length ==0 )
               { return value.length == 0 ; }
            var yearFirstExp = new RegExp("^\\s*((\\d{4})|(\\d{2}))([-/]|\\. ?)(\\d{1,2})\\4(\\d{1,2})\\.?\\s*$");
            try {
                m = value.match(yearFirstExp);
                var day, month, year;
                if (m != null && (m[2].length == 4 || params.dateorder == "ymd")) {
                    day = m[6];
                    month = m[5];
                    year = (m[2].length == 4) ? m[2] : GetFullYear(parseInt(m[3], 10));
                }
                else {
                    if (params.dateorder == "ymd") {
                        return null;
                    }
                    var yearLastExp = new RegExp("^\\s*(\\d{1,2})([-/]|\\. ?)(\\d{1,2})(?:\\s|\\2)((\\d{4})|(\\d{2}))(?:\\s\u0433\\.)?\\s*$");
                    m = value.match(yearLastExp);
                    if (m == null) {
                        return null;
                    }
                    if (params.dateorder == "mdy") {
                        day = m[3];
                        month = m[1];
                    }
                    else {
                        day = m[1];
                        month = m[3];
                    }
                    year = (m[5].length == 4) ? m[5] : GetFullYear(parseInt(m[6], 10));
                }
                month -= 1;
                var date = new Date(year, month, day);
                if (year < 100) {
                    date.setFullYear(year);
                }
                return (typeof (date) == "object" && year == date.getFullYear() && month == date.getMonth() && day == date.getDate()) ? date.valueOf() : null;
            }
            catch (err) {
                return null;
            }
        }, "Định dạng ngày / tháng / năm.");

    jQuery.validator.addMethod("NoCharSP", function(value, element) {
            return RestrictCharSp(value) == '0' ;                            
        }, jQuery.validator.format("Lưu ý ký tự đặc biệt"));
    
    jQuery.validator.addMethod("NoWhiteSP", function(value, element) {
            return value.indexOf(" ")<0 ;                            
        }, jQuery.validator.format("Lưu ý khoảng trắng"));  // JScript File

