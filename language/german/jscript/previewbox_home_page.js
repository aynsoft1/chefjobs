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
	console.log('div id', jobId)
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
      url: "api/index.php?id=" + jobId,
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
      
            skills += `
			<form action="${pathURL}job_search.php" name="search" method="post" class='d-inline skill-tags'>
				<input type="hidden" name="action" value="search">
				<input type="hidden" name="skillTag" value="${slugGenerate(val)}">
				<button type='submit' class='btn btn-light my-1'>${val}</button>
			</form>`;
          });
          let shareCard = shareCardLink(data);
          var card = `
							<div id="sticky-anchor"></div>
							<section class="two-pane-serp-page__detail-view for-mobile ms-3" style="height: calc(100vh - 69px);">
								<div class="card card-custom" id="sticky" style="border-color:#E4E4ED;">
									<div class="card-body">

									<div class="dflex">
									<div class="flex-shrink-0">
									<div class="job-preview-img"><img alt="Recruiter Logo" class="img-fluid" src="${
										data[0].logoPath
									  }"></div>
									</div>
									<div class="flex-grow-1 mt-3">
									<h3 class="m-0" style="font-size:22px;font-weight: 500;"><a href="${
										data[0].titleLink
										}" rel="noopener noreferrer">${
										data[0].job_title
										}</a></h3>

										<div class="small text-muted">
										<span>
											<a href="${data[0].companyLink}" target="_blank" rel="noopener">
												${data[0].company_name},
											</a>
										</span>
										<span class="mx-1">
										&#8226;
										</span>
										<span>${data[0].location + "" + data[0].country_name}</span>
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
									</div>
									
									<div class="small text-muted">
									<span>${data[0].jobCategory}</span>
									<span class="mx-1">
										&#8226;
									</span>
									</div>
									<div class="mb-3 small text-muted">
									<span>Posted: ${data[0].posted_on}</span>
									<span class="mx-1">
										&#8226;
									</span>
									<span>Ends: ${data[0].apply_before}</span>
									</div>
									<!--
									<ul class="list-group list-group-horizontal d-flex align-items-center m-0">
									<li class="list-group-item list-group-item-custom" style="margin-right: 0;">
									  ${
											data[0].applyJob === "true"
											? '<span class="btn btn-md btn-success"><i class="bi bi-check-lg"></i> Applied</span>'
											: '<a class="btn btn-md btn-primary" href="' +
												data[0].applyJob +
												'" title="Apply" target="_blank" rel="noopener noreferrer">Apply Now</a>'
										}
									</li>
									<li class="list-group-item list-group-item-custom">
									  ${
											data[0].recruiter_applywithoutlogin === "Yes"
											? '<a class="btn btn-md btn-outline-primary" style="margin-left:20px;" href="' +
												data[0].applyWithoutLogin +
												'" title="Apply" target="_blank" rel="noopener noreferrer">Apply without login</a>'
											: '<span style="display: none"></span>'
										}
									</li>
									<li class="list-group-item list-group-item-custom">
									  ${
											data[0].saveJob === "true"
											? '<span class="icon-unsaved mobile-absolute-right" style="font-size:22px!important; color: green!important"><i class="bi bi-bookmark-check-fill"></i></span>'
											: '<a class="" href="' +
												data[0].saveJob +
												'"><span style="font-size:22px!important;" class="icon-unsaved mobile-absolute-right"><i class="bi bi-bookmark"></i></span></a>'
										}	
									</li>
									
									<li class="list-group-item list-group-item-custom">
									  <a href="${data[0].sendPost}" target="_blank" rel="noopener">
										  <span class="icon-unsaved" style="font-size:22px!important;"><i class="bi bi-envelope"></i></span>
									  </a>
									</li>

									<li class="list-group-item list-group-item-custom">
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
								  -->


									</div>
								  </div>


								
											
	
											<hr style="margin:20px -25px 18px -25px;border-color: #999;">

										<h3 class="pb-2 preview-title">Job Summary</h3>
										<div class="card-text">
											${data[0].job_short_description}	
										</div>
										<h3 class="pt-3 pb-2 preview-title">Job Description</h3>
										<div class="card-text">
											${data[0].job_description}
										</div>
										<h3 class="pt-3 mb-3 preview-title">Keyskills</h3>
						`;
          card +=
            "<div class='card-text skill-tag d-inlineflex' id='tagsDiv'>" +
            skills +
            "</div></div>";

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
