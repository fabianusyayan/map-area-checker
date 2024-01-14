# MAP AREA CHECKER

Map area checker is a WordPress plugin designed to determine whether a given address is inside or outside of a specified service area. Leveraging the power of Mapbox, this plugin integrates geocoding and mapping functionalities to provide a seamless experience for users.


## Usage 

**Setting up custom map**

Before use this plugin you must have a custom kml map  like this https://www.google.com/maps/d/u/3/viewer?mid=1vKj-93qsHe9Sv-pARXhiTvS-nNur6UUO&ll=-37.81735011743435%2C144.96972933511174&z=15

- Download kml 
- ![image](https://i.imgur.com/m7DcLjt.png);
- visit https://mapshaper.org/
- drop your kml and run comand `ogr2ogr -f GeoJSON output.geojson input.kml`
- export into GeoJSON 
- ![iamge](https://i.imgur.com/2jFDZXd.png)
- install and activate plugin 
- get token mapbox [here](https://account.mapbox.com/)
- here sample token `pk.eyJ1IjoibXVtdWtlbWFzYW5iYXJ1IiwiYSI6ImNscmMxMmV6YzB0czIycXByZjB6ZnA2MWcifQ.8eYTj6XvTIqdDRi2JhWTlA`
- input token and json
- input inside and outside action url
- ![image](https://i.imgur.com/9WjF9ki.png)


## Screenshot 

- ![image](https://i.imgur.com/HcWWlAk.png)
- ![image](https://i.imgur.com/C1v3TBV.png)