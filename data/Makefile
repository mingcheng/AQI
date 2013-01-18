all: clean area structs

area: 
	php areas/make_areas.php

structs: area
	php structs/make_structs.php

clean:
	rm -f *.sqlite logs/*

