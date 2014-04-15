<h3>Get User Books</h3>
<p><b>Call: </b>http://koala.lindneo.com/api/getUserBooks</p>
<p><b>Method: </b>POST</p>
<p><b>Return: </b>JSON data</p>

<br><br>

<h3>Add User Note</h3>
<p><b>Call: </b>http://koala.lindneo.com/api/addUserNote</p>
<p><b>Method: </b>POST</p>
<ul><b>Attributes:</b>
	<li><b>book_id</b>: Book Id</li>
	<li><b>page_id</b>: Page Id</li>
	<li><b>note</b>: Note</li>
</ul>
<p><b>Return: </b>JSON data</p>

<br><br>

<h3>Add User Notes</h3>
<p><b>Call: </b>http://koala.lindneo.com/api/addUserNotes</p>
<p><b>Method: </b>POST</p>
<ul><b>Attributes:</b>
	<li><b>notes</b>: JSON
		<ul>
			<li>book_id:book_id</li>
			<li>page_id:page_id</li>
			<li>note:note</li>
		</ul>
	</li>
</ul>
<p><b>Return: </b>JSON data</p>
<br><br>

<h3>Get Book Notes</h3>
<p><b>Call: </b>http://koala.lindneo.com/api/getBookNotes</p>
<p><b>Method: </b>POST</p>
<ul><b>Attributes:</b>
	<li><b>book_id</b>: Book Id</li>
</ul>
<p><b>Return: </b>JSON data</p>

<h3>Check User Book</h3>
<p><b>Call: </b>http://koala.lindneo.com/api/checkUserBook</p>
<p><b>Method: </b>POST</p>
<ul><b>Attributes:</b>
	<li><b>book_id</b>: Book Id</li>
</ul>
<p><b>Return: </b>book id if user has book. else return 0</p>