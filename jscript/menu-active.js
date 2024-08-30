// Get the current URL
var currentURL = window.location.href;

// Define the list of URLs and corresponding element IDs
var urlElementMapping = {
  "recruiter_control_panel.php": "flush-collapseOne",
  "post_job.php": "flush-collapseOne",
  "list_of_jobs.php": "flush-collapseOne",
  "recruiter_import_jobs.php": "flush-collapseOne",
  "jobseeker_resume1.php": "flush-collapseOne",
  "jobseeker_resume2.php": "flush-collapseOne",
  "jobseeker_resume3.php": "flush-collapseOne",
  "jobseeker_resume6.php": "flush-collapseOne",
  "jobseeker_resume4.php": "flush-collapseOne",
  "jobseeker_resume5.php": "flush-collapseOne",
  "resume_statistics.php": "flush-collapseOne",
  "my_resumes.php": "flush-collapseOne",
  "my_cover_letters.php": "flush-collapseOne",
  "jobseeker_control_panel.php": "flush-collapseOne",

  "search_resume.php": "flush-collapseTwo",
  "search_applicant.php": "flush-collapseTwo",
  "my_resume_search_agents.php": "flush-collapseTwo",
  "rates.php": "flush-collapseTwo",
  "list_of_resumes.php": "flush-collapseTwo",
  "jobseeker_mails.php": "flush-collapseTwo",
  "my_saved_jobs.php": "flush-collapseTwo",
  "my_applications.php": "flush-collapseTwo",
  "my_saved_searches.php": "flush-collapseTwo",

  "assessment.php": "flush-collapseThree",
  "add_quiz_report.php": "flush-collapseThree",
  "applicant_tracking.php": "flush-collapseThree",
  "jobseeker_registration_step1.php": "flush-collapseThree",
  "jobseeker_change_password.php": "flush-collapseThree",
  "jobseeker_order_history.php": "flush-collapseThree",

  "recruiter_registration.php": "flush-collapseFour",
  "company_description.php": "flush-collapseFour",
  "list_of_users.php": "flush-collapseFour",
  "recruiter_change_password.php": "flush-collapseFour",
  "job_search.php": "flush-collapseFour",
  "job_search_by_location.php": "flush-collapseFour",
  "job_search_by_industry.php": "flush-collapseFour",
  "company_profile.php": "flush-collapseFour",
  "order_history.php": "flush-collapseFour",
  "contact_list.php": "flush-collapseFour",
  "job_search_by_skill.php": "flush-collapseFour",

  "forum": "flush-collapseFive",
  "article.php": "flush-collapseFive",
  "list_of_jobfairs.php": "flush-collapseFive",
  "contact_list.php": "flush-collapseFive",
  "my-courses.php": "flush-collapseFive",
  "my-tests.php": "flush-collapseFive",
  "list_of_newsletters.php": "flush-collapseFive",

  "cv_rates.php": "flush-collapseSix",
  "recruiter_mails.php": "flush-collapseSix",
  "recruiter_ats_mails.php": "flush-collapseSix",
  "courses.php": "flush-collapseSix",
  "jobseeker_mails.php": "flush-collapseSix",
};

// Remove the "show" class from all elements
var elementIds = Object.values(urlElementMapping);
elementIds.forEach(function (id) {
  var element = document.getElementById(id);
  // Check if the "show" class exists before removing it
  if (element) {
    element.classList.remove("show");
  }
});

// Check if the current URL matches any of the defined URLs
for (var url in urlElementMapping) {
  if (currentURL.includes(url)) {
    // Add the "show" class to the desired element
    var elementId = urlElementMapping[url];
    var element = document.getElementById(elementId);
    let directElem = null;

    element.classList.add("show");

    if (url === "list_of_jobs.php") {
      const searchString = "j_status=expired";
      const searchString1 = "j_status=active";
      if (isStringInURL(currentURL, searchString)) {
        directElem = "list_of_jobs2";
      }
      if (isStringInURL(currentURL, searchString1)) {
        directElem = "list_of_jobs1";
      }
    }

    // add active class sub child button
    addActiveClass(url, directElem);
    break; // Exit the loop once a match is found
  }
}

function addActiveClass(elementId, directElem = null) {
  console.log("idk is", elementId);
  if (directElem) {
    var element = document.getElementById(directElem);
    if (element) {
      element.classList.add("subBtnActive");
    }
  } else {
    var dotIndex = elementId.indexOf(".");
    if (dotIndex !== -1) {
      elementId = elementId.substring(0, dotIndex);
    }

    if (elementId === "add_quiz_report") {
      elementId = "assessment";
    }

    var element = document.getElementById(elementId);
    if (element) {
      element.classList.add("subBtnActive");
    }
  }
}

function isStringInURL(url, searchString) {
  return url.includes(searchString);
}
