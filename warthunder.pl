#!/usr/bin/perl

use strict;
use warnings;
use CGI::Carp qw(fatalsToBrowser);
print "Content-type: application/rss+xml\n\n";


my $URL = "http://forum.warthunder.com/index.php?/forum/849-news-discussion/";

my $html = `wget -O - $URL`;
my @lines = split /[\r\n]+/, $html;

my @threads = ();

foreach my $line (@lines) {
	if ($line =~ /<a itemprop="url" id="(?:[^"]+)" href="([^"]+)" title='([^']+)'/) {
		push @threads, { url => $1, title => $2 };
	}
}

print q{<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">

<channel>
  <title>War Thunder News</title>
  <link>}.$URL.q{</link>
  <description></description>
};

foreach my $thread (@threads) {
	print qq{
  <item>
    <title>$thread->{title}</title>
    <link>$thread->{url}</link>
    <description>$thread->{title}</description>
  </item>
};
}

print q{
</channel>
</rss>
};
