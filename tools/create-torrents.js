var fs 		= require( 'fs' );
var nt 		= require( 'nt' );

var files = fs.readdirSync('../data/videos');

for(var inputfile in files) {
	var rs = nt.makeWrite('../data/torrent/'+ files[inputfile] +'.torrent',
		"udp://tracker.openbittorrent.com:80/announce",
		 '../data/videos',
		 files[inputfile],
		 function(err, torrent){
		 	if (err) throw err;
		 	console.log('Torrent ' + files[inputfile] + ' created!');
	});
}