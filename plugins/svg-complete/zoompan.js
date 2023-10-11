
var totalX = 0;
var totalY = 0;
var scale = 1;
var toolTipcnt= 0;
var currentID='NoToolTip';
var oldCurrentID;
var Xbegin;
var Ybegin;
var Xmove;
var Ymove;
var Dragging= 0;

jQuery( ".svgNoZoomContainer" ).mouseenter(function( event ) {
   currentID= 'NoToolTip'; 
   jQuery('.svgTooltip').hide();
});

jQuery( ".svgcontainer" ).mouseenter(function( event ) {
    var matrixToArray;
    var thisMatrix;
    
    jQuery('.svgTooltip').show();
    
    currentID = jQuery(this).attr('id');
    thisMatrix = jQuery('#transmatrix.'+currentID).attr('transform');
    matrixToArray= thisMatrix.match(/(-?[0-9\.]+)/g);
    if(currentID!=oldCurrentID){
       scale= parseFloat(matrixToArray[0]); 
       totalX= parseFloat(matrixToArray[4]);
       totalY= parseFloat(matrixToArray[5]);
       //translate( totalX, totalY );
    }
    oldCurrentID= currentID;
});


function translate( x, y )
{
	//var clientArea = document.getElementById( "transmatrix" );
	var transform = "matrix(" + scale + ",0,0," + scale + "," + x + "," + y + ")";
        jQuery( "#transmatrix." +currentID ).attr( "transform", transform  );
	//transmatrix.setAttribute( "transform", transform );
}

function zoomIn()
{
	scale += 0.3;
        totalX -= 50;
        totalY -= 50;
	translate( totalX, totalY );
}

function zoomOut()
{
	if (scale>0.6) { 
            scale -= 0.3;
            totalX += 50;
            totalY += 50;
        }
        translate( totalX, totalY );
}

function viewAll()
{
	scale = 1;
	totalX = 0;
	totalY = 0;
	translate( totalX, totalY );
}

function panLeft()
{
	totalX -= 15;
	translate( totalX, totalY );
}

function panRight()
{
	totalX += 15;
	translate( totalX, totalY );
}
function panUp()
{
	totalY -= 15;
	translate( totalX, totalY );
}
function panDown()
{
	totalY += 15;
	translate( totalX, totalY );
}


jQuery('.zoomInButton').click(function(){ zoomIn(); });
jQuery(".zoomOutButton").click(function(){ zoomOut(); });
jQuery('.viewAllButton').click(function(){ viewAll(); });
jQuery(".panLeftButton").click(function(){ panLeft(); });
jQuery(".panRightButton").click(function(){ panRight(); });
jQuery(".panUpButton").click(function(){ panUp(); });
jQuery(".panDownButton").click(function(){ panDown(); });

jQuery(".svgimg").dblclick(function(){ viewAll(); });


jQuery( ".svgimg" ).mousedown(function( event ) {
  Xbegin= event.pageX;
  Ybegin= event.pageY;
  Dragging= 1;
  EndTooltip();
});

jQuery( ".svgimg" ).mousemove(function( event ) {
//    thisMatrix = jQuery('#transmatrix.'+currentID).attr('transform');
//    matrixToArray= thisMatrix.match(/(-?[0-9\.]+)/g);
//    jQuery( "#log." +currentID ).text( 'Current img: '+currentID  );
//    jQuery( "#log." +currentID ).append( '<br/>'  );
//    jQuery( "#log." +currentID ).append( 'Old img: '+oldCurrentID  );
//    jQuery( "#log." +currentID ).append( '<br/>'  );
//    jQuery( "#log." +currentID ).append( 'Transform= '+thisMatrix  );
//    jQuery( "#log." +currentID ).append( '<br/>'  );
//    jQuery( "#log." +currentID ).append( 'Matrix array= '+matrixToArray );
//    jQuery( "#log." +currentID ).append( '<br/>'  );
//    jQuery( "#log." +currentID ).append( 'scale= '+matrixToArray[0]  );
//    jQuery( "#log." +currentID ).append( '<br/>'  );
//    jQuery( "#log." +currentID ).append( 'X= '+matrixToArray[4] );
//    jQuery( "#log." +currentID ).append( '<br/>'  );
//    jQuery( "#log." +currentID ).append( 'Y= '+matrixToArray[5]  );
    
    if(Dragging){  
      Xmove= event.pageX - Xbegin;
      Ymove= event.pageY - Ybegin;
      translate( totalX+Xmove, totalY+Ymove );
    }
});

jQuery(document.body).mouseup(function( event ) {
  if(Dragging){
      EndTooltip();
      Dragging= 0;
      document.getSelection().removeAllRanges();            //deselect text if it gets selected by accident
      totalX += Xmove;
      totalY += Ymove;
  }
});

jQuery( ".svgimg" ).on('DOMMouseScroll mousewheel', function (e) {
  if(e.originalEvent.detail > 0 || e.originalEvent.wheelDelta < 0) { //alternative options for wheelData: wheelDeltaX & wheelDeltaY
     zoomOut();
  } else {
     zoomIn();
  }
  if (scale<0.5) scale= 0.5;
  EndTooltip();
  translate( totalX, totalY );
  //prevent page fom scrolling
  return false;
});

// debug

//jQuery(document.body).mousemove(function( event ) {
//    console.log('Tooltip teller: ' + toolTipcnt);
//});

//Tooltip

function EndTooltip(){
   if(toolTipcnt<100) jQuery('.svgTooltip').fadeOut(1000,function(){ jQuery('.svgTooltip').remove() });
   toolTipcnt= 100;
}

jQuery(document).ready(function() {
    //if(currentID!='NoToolTip'){
        jQuery('.svgimg').hover(function(){
            //if(currentID=='NoToolTip'){
                    // Hover over code
                    var title = 'Drag and zoom with your mouse';
                    if(currentID!='NoToolTip') toolTipcnt++;
                    if(toolTipcnt<4){
                        jQuery('<p class="svgTooltip" style="position:absolute; z-index:100;"></p>')
                        .text(title)
                        .appendTo('body')
                        .fadeIn(1500);
                    } else jQuery('.svgTooltip').remove();
           // }
        }, function() {
                // Hover out code
                jQuery('.svgTooltip').fadeOut(1000);//.remove();
        }).mousemove(function(e) {
                var mousex = e.pageX + 10; //Get X coordinates
                var mousey = e.pageY + 5; //Get Y coordinates
                jQuery('.svgTooltip')
                .css({ top: mousey, left: mousex })
        });
    //}
});