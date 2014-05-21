<html>
    <head>
        <title>
            Wordpress Mysql Replace
        </title>
    </head>
    <body>
        <form name="replace" action="replace.php" method="GET">
            <table>
                <tr>
                    <td>
                        Old Host Value
                    </td>
                    <td>
                        <input name="old_host" value="" />
                    </td>
                </tr>
                <tr>
                    <td>
                        New Host Value
                    </td>
                    <td>
                        <input name="new_host" value="" />
                    </td>
                </tr>
                <tr>
                    <td>
                        DataBase Host
                    </td>
                    <td>
                        <input name="db_host" value="localhost" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Database User
                    </td>
                    <td>
                        <input name="db_user" value="" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Database Password
                    </td>
                    <td>
                        <input name="db_pass" value="" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Database Name
                    </td>
                    <td>
                        <input name="db_db" value="" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Database Prifix
                    </td>
                    <td>
                        <input name="db_prefix" value="wp_" />
                    </td>
                </tr>
            </table>
	<input type="submit" value="Replace" />
        </form>
    </body>
</html>
