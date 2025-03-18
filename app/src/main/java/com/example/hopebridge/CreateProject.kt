    package com.example.hopebridge

    import android.app.Activity
    import android.content.Intent
    import android.content.SharedPreferences
    import android.net.Uri
    import android.os.Bundle
    import android.provider.MediaStore
    import android.util.Log
    import android.view.View
    import android.view.ViewGroup
    import android.widget.*
    import androidx.activity.enableEdgeToEdge
    import androidx.appcompat.app.AppCompatActivity
    import retrofit2.Call
    import retrofit2.Callback
    import retrofit2.Response

    import retrofit2.http.*

    interface ProjectService {

        @FormUrlEncoded
        @POST("HopeBridge_Web/project_php/create_project.php")
        fun saveProject(
            @Field("user_id") userId: Int,
            @Field("project_name") projectName: String,
            @Field("project_summary") projectSummary: String,
            @Field("donation_goal") donationGoal: String,
            @Field("image_url") imageUrl: String?,
            @Field("organization_id") organizationId: Int
        ): Call<String>

        @FormUrlEncoded
        @POST("HopeBridge_Web/project_php/donate_project.php")
        fun saveDonation(
            @Field("user_id") userId: Int,
            @Field("project_name") projectName: String,
            @Field("amount") amount: Double
        ): Call<String>

        @GET("HopeBridge_Web/project_php/fetch_projects.php")
        fun fetchProjects(): Call<List<Project>>

        @GET("HopeBridge_Web/project_php/fetch_funds.php")
        fun getFundsRaised(@Query("project_name") projectName: String): Call<Project>
    }


    class CreateProject : AppCompatActivity() {
        private lateinit var imageAdd: LinearLayout
        private lateinit var imageView: ImageView
        private var imageUri: Uri? = null
        private lateinit var nameEditText: EditText
        private lateinit var summaryEditText: EditText
        private lateinit var submitButton: Button
        private val PICK_IMAGE_REQUEST = 1
        private lateinit var sharedPreferences: SharedPreferences


        override fun onCreate(savedInstanceState: Bundle?) {
            super.onCreate(savedInstanceState)
            setContentView(R.layout.activity_create_project)

            // Back button functionality
            val backbutton: ImageView = findViewById(R.id.back)
            backbutton.setOnClickListener {
                val intent = Intent(this, Homepage::class.java)
                startActivity(intent)
            }

            // Spinner setup
            val spinner: Spinner = findViewById(R.id.environmentSpinner)
            val options = listOf("Please Select", "Red Cross", "UNICEF", "WWF" , "Doctors Without Borders" , "Save the Children")

            sharedPreferences = getSharedPreferences("userPrefs", MODE_PRIVATE)

            val adapter = object : ArrayAdapter<String>(
                this, android.R.layout.simple_spinner_item, options
            ) {
                override fun getView(position: Int, convertView: View?, parent: ViewGroup): View {
                    val view = super.getView(position, convertView, parent)
                    if (position == 0) {
                        (view as TextView).text = "Please Select"
                        view.setTextColor(resources.getColor(android.R.color.darker_gray))
                    }
                    return view
                }
            }

            adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
            spinner.adapter = adapter

            // Image selection setup
            imageAdd = findViewById(R.id.imageadd)
            imageView = findViewById(R.id.imageIcon)

            imageAdd.setOnClickListener {
                val intent = Intent(Intent.ACTION_PICK, MediaStore.Images.Media.EXTERNAL_CONTENT_URI)
                startActivityForResult(intent, PICK_IMAGE_REQUEST)
            }

            nameEditText = findViewById(R.id.name)
            summaryEditText = findViewById(R.id.summproject)
            submitButton = findViewById(R.id.submitButton)

            submitButton.setOnClickListener {
                val projectName = nameEditText.text.toString()
                val projectSummary = summaryEditText.text.toString()
                val userId = sharedPreferences.getInt("user_id", -1)
                Log.d("CreateProject", "Retrieved user_id: $userId")
                val donationGoal = findViewById<EditText>(R.id.donategoal).text.toString()

                // Get selected organization ID based on the spinner selection
                val selectedOrganization = spinner.selectedItem.toString()
                val organizationId = when (selectedOrganization) {
                    "Red Cross" -> 1
                    "UNICEF" -> 2
                    "WWF" -> 3
                    "Doctors Without Borders" -> 4
                    "Save the Children" -> 5
                    else -> -1  // Default invalid ID
                }

                if (projectName.isNotEmpty() && projectSummary.isNotEmpty() && donationGoal.isNotEmpty() && userId != -1 && organizationId != -1) {
                    val projectService = ApiClient.getRetrofitInstance().create(ProjectService::class.java)
                    val call = projectService.saveProject(userId, projectName, projectSummary, donationGoal, imageUri?.toString(), organizationId)

                    call.enqueue(object : Callback<String> {
                        override fun onResponse(call: Call<String>, response: Response<String>) {
                            if (response.isSuccessful) {
                                val projectId = response.body()?.toIntOrNull() ?: -1
                                if (projectId != -1) {
                                    sharedPreferences.edit().putInt("project_id", projectId).apply()
                                }

                                Toast.makeText(this@CreateProject, "Project saved!", Toast.LENGTH_SHORT).show()

                                val resultIntent = Intent()
                                resultIntent.putExtra("projectName", projectName)
                                resultIntent.putExtra("projectSummary", projectSummary)
                                resultIntent.putExtra("imageUri", imageUri.toString())
                                resultIntent.putExtra("donationGoal", donationGoal)
                                resultIntent.putExtra("organizationId", organizationId) // Add organization ID
                                setResult(Activity.RESULT_OK, resultIntent)
                                finish()
                            } else {
                                Toast.makeText(this@CreateProject, "Failed to save", Toast.LENGTH_SHORT).show()
                            }
                        }

                        override fun onFailure(call: Call<String>, t: Throwable) {
                            Toast.makeText(this@CreateProject, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
                        }
                    })
                } else {
                    Toast.makeText(this, "Please fill all fields and select an organization", Toast.LENGTH_SHORT).show()
                }
            }
}

            override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
            super.onActivityResult(requestCode, resultCode, data)
            if (requestCode == PICK_IMAGE_REQUEST && resultCode == Activity.RESULT_OK && data != null) {
                imageUri = data.data
                imageView.setImageURI(imageUri)
                imageAdd.background = null

                val dragText: TextView = findViewById(R.id.dragText)
                dragText.visibility = View.GONE

                val layoutParams = imageView.layoutParams
                layoutParams.width = ViewGroup.LayoutParams.MATCH_PARENT // Full width
                layoutParams.height = 600 // Adjust this value as needed
                imageView.layoutParams = layoutParams
            }
        }
    }
