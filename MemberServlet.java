import java.io.IOException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

@WebServlet("/MemberServlet")
public class MemberServlet extends HttpServlet {
    private static final long serialVersionUID = 1L;

    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
        String displayMethod = request.getParameter("display_method");
        String currency = request.getParameter("currency");

        // Here you can add code to save the settings data to a database or process it further

        response.setContentType("text/html");
        response.getWriter().println("<h1>Settings Updated Successfully</h1>");
        response.getWriter().println("<p>Display Method: " + displayMethod + "</p>");
        response.getWriter().println("<p>Currency: " + currency + "</p>");
    }
}
//  <script>
//         import java.io.IOException;
// import javax.servlet.ServletException;
// import javax.servlet.annotation.WebServlet;
// import javax.servlet.http.HttpServlet;
// import javax.servlet.http.HttpServletRequest;
// import javax.servlet.http.HttpServletResponse;

// @WebServlet("/MemberServlet")
// public class MemberServlet extends HttpServlet {
//     private static final long serialVersionUID = 1L;

//     protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
//         String fullName = request.getParameter("fullName");
//         String userName = request.getParameter("userName");
//         String password = request.getParameter("password");
//         String role = request.getParameter("role");
//         String email = request.getParameter("email");
//         String address = request.getParameter("address");

//         // Here you can add code to save the member data to a database or process it further

//         response.setContentType("text/html");
//         response.getWriter().println("<h1>Member Added Successfully</h1>");
//         response.getWriter().println("<p>Full Name: " + fullName + "</p>");
//         response.getWriter().println("<p>User Name: " + userName + "</p>");
//         response.getWriter().println("<p>Role: " + role + "</p>");
//         response.getWriter().println("<p>Email: " + email + "</p>");
//         response.getWriter().println("<p>Address: " + address + "</p>");

//     }
// }