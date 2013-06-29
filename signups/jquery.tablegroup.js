(function( $ ) {
  $.fn.tablegroup = function(startCol, numCols, subtotal) {
  //Created By: Brij Mohan http://techbrij.com/html-table-row-grouping-jquery
			
			function groupTable($rows, startIndex, total) {
				if (total === 0) {
					return;
				}
				var i, currentIndex = startIndex,
					count = 1,
					lst = [];
				var tds = $rows.find('td:eq(' + currentIndex + ')');
				var ctrl = $(tds[0]);
				lst.push($rows[0]);
				for (i = 1; i <= tds.length; i++) {
					if (ctrl.text() == $(tds[i]).text()) {
						count++;
						$(tds[i]).addClass('deleted');
						lst.push($rows[i]);
					} else {
						if (count > 1) {
							ctrl.addClass('spanned').attr('rowspan', count);
							groupTable($(lst), startIndex + 1, total - 1)
						}
						count = 1;
						lst = [];
						ctrl = $(tds[i]);
						lst.push($rows[i]);
					}
				}
			}
				groupTable(this.find('tr:has(td)'), startCol, numCols);
				this.find('.deleted').hide();
				
				if (subtotal) {
					this.find('.spanned').each( function() {
						$(this).append($('<small class="subtotal" />').text(' ('+$(this).attr('rowspan')+')'));
					});
				}

				return this;
  };
})( jQuery );