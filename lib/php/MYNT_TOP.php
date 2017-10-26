<?php

class MYNT_TOP{

	// blog-article-lists
	// use : <<METHOD:MYNT_TOP::viewBlogLists()>>
	public static function viewBlogLists($dir = "data/page/"){
		MYNT_BLOG::viewArticleLists_li();
	}


}
