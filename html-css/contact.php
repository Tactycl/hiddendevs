<!DOCTYPE html>
<html>
    <head>
        <meta name="description" content="Roblox Game-Maker Studio" />
        <meta name="keywords" content="Roblox, Tarylem, Game-Maker Studio, Games, Roblox Games, Racing Games">
        <meta name="author" content="Tarylem, Tactical">
        <meta name="viewport" content="with=device-width, initial-scale=1.0">
        <title>Tarylem</title>
        <link rel="icon" type="image/x-icon" href="img/favicon.ico"/>
        <link rel="stylesheet" href="styles/style.css">
        <link rel="stylesheet" href="styles/fonts.css">
        <link rel="stylesheet" href="styles/all.min.css">
    </head>
    <body>
        <!-- same header as in home.php but half the height. It has some contact buttons and the profile button with an id for my JS script, it also has a header. -->
        <section class="sub-header">
            <nav>
                <a href="home"><img src="img/logo.png"></a>
                <div class="nav-links" id="navLinks">
                    <ul>
                        <li><a href="home">HOME</a></li>
                        <li><a href="about">ABOUT</a></li>
                        <li><a href="team">TEAM</a></li>
                        <li><a href="contact">CONTACT</a></li>
                        <li><a href="profile" id="profile-button">PROFILE</a></li>
                    </ul>
                </div>
                <i class="fa fa-bars"></i>
            </nav>

            <h1>Contact Us</h1>

        </section>

        <!-- A note in the middle of the screen for users to be informed about me collecting their IP with the contactform.php -->
        <p class="note-text"> Note: We collect your IP to block E-Mails if necessary. By sending us an E-Mail via this form, you agree to us collecting your IP-Address. </p>
        <section class="contact-us">

            <!-- This is just a row (vertical this time) containing information, at the moment just one (contact email) it creates some <div>s as containers and <i>s as icons. h5 as header 5 and some text. -->
            <div class="row">
                <div class="contact-col">
                    <div>
                        <i class="fa-solid fa-envelope"></i>
                        <span>
                            <h5>info@tarylem.com</h5>
                            <p>Email us your query</p>
                        </span>
                    </div>
                </div>
                <div class="contact-col">
          <!-- contact form to contact me: name, email, subject and a message. A submit button to be used by the contactform.php -->
					<form action="phpforms/contactform.php" method="post">
						
                        <input type="text" name="name" placeholder="First Name [optional]">
                        <input type="email" name="mail" placeholder="Email Address [required]" required>
                        <input type="Subject" name="subject" placeholder="Your Subject [required]" required>
                        <textarea rows="8" name="text" placeholder="Message [required]" required></textarea>
                        <button type="submit" class="hero-btn-2">Send Message</button>
                    </form>
                </div>
            </div>

        </section>

        <!-- IGNORE -->
        <?php include "includes/footer.php"?>
        <!-- JS inserts -->
        <script src="js/navui.js" defer></script>
        <script src="js/scroll.js" defer></script>
        <script src="js/loadprofile.js" defer></script>
    </body>
</html>
