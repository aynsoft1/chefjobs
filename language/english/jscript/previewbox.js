$(document).ready(function () {
  getId();

  function getId(params) {
    const ids = $(".previewBox").attr("id");
    if (ids) {
      loadPreviewBox(ids);
    }
  }

  // generate slug
  function slugGenerate(value = "") {
    $tag = value.trim();
    $search_tag = value
      .replace(/ +/g, "+")
      .replace("/", "_")
      .replace("\\", "_");
    return $search_tag;
  }

  // click box event fire
  $(".previewBox").click(function () {
    const jobId = this.id;
    $(".previewBox.grid-active").removeClass("grid-active");
    $(this).addClass("grid-active");
    loadPreviewBox(jobId);
  });

  function shareCardLink(data) {
    const cardLink = `
				<a href='https://facebook.com/sharer/sharer.php?u=${data[0].titleLink}' target="_blank" rel="noopener" aria-label="share on facebook">
					<i class="fab fa-facebook-square fa-2x"></i>
				</a>
				<a href='https://twitter.com/intent/tweet/?url=${data[0].titleLink}' target="_blank" rel="noopener" aria-label="share on twitter">
					<i class="fab fa-twitter-square fa-2x"></i>
				</a>
				<a href='https://www.linkedin.com/sharing/share-offsite/?url=${data[0].titleLink}' target="_blank" rel="noopener" aria-label="share on linkedin">
					<i class="fab fa-linkedin fa-2x"></i>
				</a>
			`;
    return cardLink;
  }
  function loadPreviewBox(jobId) {

    $.ajax({
      url: base_url+"api/index.php?id=" + jobId,
      method: "GET",
      dataType: "JSON",
      success: function (result) {
        if (result !== "false") {
          const data = result;
          const skillsArray = data[0].job_skills
            ? data[0].job_skills.split(",")
            : null;
          const pathURL = data[0].yourHomeURL;
          let skills = "";
          jQuery.each(skillsArray, function (i, val) {
             skills +=
               "<a href='" +
                pathURL +
               "jobskill/" +
                slugGenerate(val) +
                "-jobs'>" +
                val +
                "</a>";
            
          });
          let shareCard = shareCardLink(data);
          var card = `
		<div id="sticky-anchor"></div>
		<section class="two-pane-serp-page__detail-view for-mobile" style="height: calc(100vh - 69px);">
			<div class="card card-custom" id="sticky">
				<div class="card-body">

				<div class="">
				<div class="d-flex align-items-center">
				<div class="flex-shrink-0">
				<div class="job-preview-img ms-1"><img class="img-fluid" src="${
					data[0].logoPath
				  }">
				</div>
				</div>
				
				<div class="ms-auto me-4">
				  ${
					data[0].saveJob === "true"
						? '<span class="icon-unsaved mobile-absolute-right" style="font-size:22px!important; color: #0052FF!important"><i class="bi bi-heart-fill"></i></span>'
						: '<a class="" href="' +
							data[0].saveJob +
							'"><span style="font-size:22px!important;" class="icon-unsaved mobile-absolute-right"><i class="bi bi-heart"></i></span></a>'
					}	
				</div>
				</div>
				<div class="flex-grow-1 ms-1">
				<h3 class="job-title m-0 mt-3 mb-2" style="font-size:26px;"><a href="${
					data[0].titleLink
					}" target="_blank" rel="noopener noreferrer">${
					data[0].job_title
					}</a></h3>

					<div class="">
					<span>
						<a class="clink text-blue" href="${data[0].companyLink}" target="_blank" rel="noopener">
							${data[0].company_name}
						</a>
					</span>
					<span class="mx-1">
					&#8226;
					</span>
					<span> ${data[0].location + " " + data[0].country_name}</span>
				</div>
				<div class="m-0 small text-muted"><span>${data[0].jobType}</span>
				<span class="mx-1">
					&#8226;
				</span>
				<span>${data[0].totalSalary}</span>
				<span class="mx-1">
					&#8226;
				</span>
				<span>${data[0].totalExperience}</span>
				<span class="mx-1">
					&#8226;
				</span>
				<span>${data[0].jobCategory}</span>
				</div>
				<div class="mb-3 small text-muted">
				<span>Posted: ${data[0].posted_on}</span>
				<span class="mx-1">
					&#8226;
				</span>
				<span>Ends: ${data[0].apply_before}</span>
				</div>
				<ul class="list-group list-group-horizontal d-flex align-items-center m-0">
				<li class="list-group-item list-group-item-custom" style="margin-right: 0;">
				  ${
					
data[0].applyJob === "true"
? '<span class="btn btn-md btn-success"><i class="bi bi-check-lg"></i> Applied</span>'
: data[0].applyJob == '' ? '' : '<a class="btn btn-md btn-primary btn-primary2" href="' +
data[0].applyJob +
'" title="Apply" target="_blank" rel="noopener noreferrer">Apply<i class="bi bi-box-arrow-in-up-right ms-2"></i></a>'
}
				</li>
				<li class="list-group-item list-group-item-custom me-3">
				  ${
(data[0].recruiter_applywithoutlogin === "Yes" && data[0].applyWithoutLogin !== '')
? '<a class="btn btn-md btn-outline-primary btn-outline-primary-cus" style="margin-left:20px;" href="' +
data[0].applyWithoutLogin +
'" title="Apply" target="_blank" rel="noopener noreferrer">Apply without login</a>'
: '<span style="display: none"></span>'
}
				</li>
				
				<li class="list-group-item list-group-item-custom me-3">
				  <a href="${data[0].sendPost}" target="_blank" rel="noopener">
					  <span class="icon-unsaved" style="font-size:22px!important;"><i class="bi bi-envelope"></i></span>
				  </a>
				</li>

				<li class="list-group-item list-group-item-custom me-3">
					<div class="dropdown">
					<button class="btn-share-job-preview p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false"">
					  <span style="color: #a8a8a8;"><i class="bi bi-share"></i></span>
					</button>
					<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
					  <a class="dropdown-item" href='https://facebook.com/sharer/sharer.php?u=${
data[0].titleLink
}' target="_blank" rel="noopener" aria-label="share on facebook">
<i class="bi bi-facebook text-facebook"></i> Facebook</a>
					  <a class="dropdown-item" href='https://twitter.com/intent/tweet/?url=${
data[0].titleLink
}' target="_blank" rel="noopener" aria-label="share on twitter">
<i class="bi bi-twitter text-twitter"></i> Twitter</a>
					  <a class="dropdown-item" href='https://www.linkedin.com/sharing/share-offsite/?url=${
data[0].titleLink
}' target="_blank" rel="noopener" aria-label="share on linkedin">
<i class="bi bi-linkedin text-linkedin"></i> Linkedin</a>
					</div>
				  </div>
			  </li>
			  </ul>


				</div>
			  </div>


			
						

				<hr style="margin:20px -25px 18px -25px;border-top: 1px solid #a7adb3;">
				<div class="ms-1">
					<h4 class="mb-2" style="font-size:20px;font-weight:bold;">Job Summary</h4>
					<div class="card-text">
						${data[0].job_short_description}	
					</div>
					<h4 class="mb-2 mt-4" style="font-size:20px;font-weight:bold;">Job Description</h4>
					<div class="card-text">
						${data[0].job_description}
					</div>
					<div class="fw-bold mb-2 mt-4">Keyskills</div>
	`;
card +=
"<div class='card-text skill-tag d-inlineflex' id='tagsDiv'>" +
skills +
"</div></div></div>";

card += "<div class='card-footer3 card-footer-custom3'>";
card += "</div></div></section>";

          $("#previewDiv").html(card);
        } else {
          console.warn("parameter missing in job api");
        }
      },
    });
  }
});
