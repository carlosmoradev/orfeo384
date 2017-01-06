#!/bin/sed -f
s/"orfeo"/"orfeo_db"/
s/$usuario = "postgres";/$usuario = "orfeo_user";/
s/123/0rf30p4ssw0rd/
s/ORGANIZACION/EntidadDePrueba/
s#/var/www/3.8.2des/#/var/www/orfeo384/#
