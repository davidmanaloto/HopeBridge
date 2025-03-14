package com.example.hopebridge

import android.content.Intent
import android.content.res.Resources
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.view.View
import android.view.animation.AlphaAnimation
import android.widget.Button
import android.widget.ImageView
import android.widget.TextView
import android.widget.Toast
import androidx.activity.enableEdgeToEdge
import androidx.appcompat.app.AppCompatActivity
import androidx.constraintlayout.widget.ConstraintLayout
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat

class MainActivity : AppCompatActivity() {
    private var isPopupShown = false // Flag to check if the popup is shown
    private var backPressedTime: Long = 0
    private val backPressedDelay: Long = 3000 // 3 seconds

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContentView(R.layout.activity_main)


        val heightOfScreen = Resources.getSystem().displayMetrics.heightPixels
        val startlogo = findViewById<ImageView>(R.id.startlogo)
        val starttitle = findViewById<TextView>(R.id.starttitle)
        val main = findViewById<ConstraintLayout>(R.id.main)


        val popup = listOf<View>(
            findViewById(R.id.message),
            findViewById(R.id.btnSignIn),
            findViewById(R.id.btnRegister)
        )

        popup.forEach { it.visibility = View.GONE }


        // ANIMATION NG LOGO
        val logoAnimation = AlphaAnimation(0f, 1f).apply {
            duration = 1000
            fillAfter = true
        }
        startlogo.startAnimation(logoAnimation)

        // ANIMATION NG TITLE
        val fadeInAnimation = AlphaAnimation(0f, 1f).apply {
            duration = 2000
            fillAfter = true
        }
        starttitle.startAnimation(fadeInAnimation)

        // LISTENER PRA MAG GO YUNG ANIMATION NG POPUP
        main.setOnClickListener {
            if (!isPopupShown) {
                showPopup(popup, heightOfScreen)
                animateLogoAndTitle()
                isPopupShown = true // Set flag that the popup is shown
            }
        }

        // CLICK LISTENER NG SIGN IN BUTTON
        val signinButton = findViewById<Button>(R.id.btnSignIn)
        signinButton.setOnClickListener {
            // Handle the sign-in button click
            val intent = Intent(this, Signin::class.java)
            startActivity(intent)
        }

        //CLICK LISTENER NG SIGN UP BUTTON
        val signupbutton = findViewById<Button>(R.id.btnRegister)
        signupbutton.setOnClickListener {

            val intent = Intent(this, Signup::class.java)
            startActivity(intent)
        }
    }

    override fun onBackPressed() {
        if (backPressedTime + backPressedDelay > System.currentTimeMillis()) {
            // Exit the app if back button is pressed again within the delay
            super.onBackPressed() // This will finish the activity and exit the app
            return
        } else {
            // Show a toast message for the first back press
            Toast.makeText(this, "Press back again to close the app", Toast.LENGTH_SHORT).show()
        }
        backPressedTime = System.currentTimeMillis()
    }



    // POPUP ANIMATION
    private fun showPopup(popupViews: List<View>, heightOfScreen: Int) {
        popupViews.forEach { view ->
            view.visibility = View.VISIBLE
            view.translationY = heightOfScreen.toFloat() // Start below the screen
            view.animate()
                .translationY(-200f) // Move to original position
                .setDuration(500)
                .start()
        }
    }

    // ANIMATION NG LOGO AND TITLE PRA UMANGAT PAG UMANGAT NA SI POPUP
    private fun animateLogoAndTitle() {
        val startlogo = findViewById<ImageView>(R.id.startlogo)
        val starttitle = findViewById<TextView>(R.id.starttitle)

        // DITO SA LOGO
        startlogo.animate()
            .translationY(-300f) // Adjust as needed
            .setDuration(500) // Duration of the animation
            .start()

        // DITO SA TITLE
        starttitle.animate()
            .translationY(-300f) // Adjust as needed
            .setDuration(500) // Duration of the animation
            .start()
    }
}
