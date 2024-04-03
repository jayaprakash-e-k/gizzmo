function message(msg, type) {
    var html = `
        <div class="msg-container ${type}">
			<div class="msg">
				<span class="icon">
                ${  type == 'success' ?
                    '<svg viewBox="0 0 300 300" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M103 147.5L133 182L195.5 119" stroke="#00E532" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" class="svg-elem-1"></path><circle cx="143.5" cy="153.5" r="107.5" stroke="#00E532" stroke-width="14" class="svg-elem-2"></circle></svg>'
                    :
                    '<svg width="300" height="300" viewBox="0 0 300 300" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M163.861 57.75C162.343 55.1201 159.537 53.5 156.5 53.5C153.463 53.5 150.657 55.1201 149.139 57.75L58.6391 214.5C57.1207 217.13 57.1207 220.37 58.6391 223C60.1575 225.63 62.9636 227.25 66.0003 227.25H247C250.036 227.25 252.842 225.63 254.361 223C255.879 220.37 255.879 217.13 254.361 214.5L163.861 57.75Z" stroke="#DC3545" stroke-width="17" stroke-linejoin="round" class="svg-elem-11"></path><path d="M157 112.5V163.5" stroke="#DC3545" stroke-width="21" stroke-linecap="round" stroke-linejoin="round" class="svg-elem-12"></path><circle cx="157" cy="194" r="10" fill="#DC3545" class="svg-elem-13"></circle></svg>'
                }
				</span>

				<span class="text">
                    ${msg}
				</span>
			</div>
		</div>
    `;

    $("body").append(html);

    setTimeout(() => {
        $('.msg-container').remove();
    }, 3000);
}