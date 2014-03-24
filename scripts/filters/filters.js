app.filter('getById', function() {
  return function(input, id) {
    
    var i=0, len=input.length;
    for (; i<len; i++) {
      if (+input[i].id == +id) {
        return input[i];
      }
    }
    return null;
  }
});


app.filter('getIndexById', function() {
  return function(input, id) {
    
    var i=0, len=input.length;
    for (; i<len; i++) {
      if (+input[i].id == +id) {
        return i;
      }
    }
    return null;
  }
});

/**********************************
 * Truncate Filter
 * @Param text
 * @Param length, default is 10
 * @Param end, default is "..."
 * @return string
 ***********************************/
app.filter('truncate', function(){
        return function (text, length, end) {
            if (isNaN(length))
                length = 10;
 
            if (end === undefined)
                end = "...";
 
            if (text.length <= length || text.length - end.length <= length) {
                return text;
            }
            else {
                return String(text).substring(0, length-end.length) + end;
            }
 
        };
});



/************************************
 * noCache Filter
 * @return string + ?cb=timeStamp
 ***********************************/    
app.filter('noCache', function() {
  return function(input) {
    
    var random = (new Date()).toString();
	input = input + "?cb=" + random;
					
    return input;
  }
});   



