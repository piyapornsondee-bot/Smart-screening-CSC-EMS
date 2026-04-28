<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Screening Program</title>
    <meta name="description" content="ระบบคัดกรองและแนะนำห้องตรวจ OPD สำหรับเจ้าหน้าที่โรงพยาบาล">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="glass-theme">
    <!-- Decorative background elements -->
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>
    <div class="bg-blob blob-3"></div>

    <!-- Top Bar -->
    <header id="top-bar">
        <div class="top-bar-inner">
            <div class="brand">
                <div class="brand-icon">
                    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Bolder, stylized S -->
                        <path d="M30 32C30 25.4 35.4 20 42 20H80V36H46V44H70C78.3 44 85 50.7 85 59V61C85 69.3 78.3 76 70 76H30V60H69V52H45C36.7 52 30 45.3 30 37V32Z" fill="white"/>
                        <!-- Digital pixel accents -->
                        <rect x="12" y="44" width="12" height="12" fill="white" opacity="0.8"/>
                        <rect x="24" y="58" width="10" height="10" fill="white" opacity="0.5"/>
                        <rect x="12" y="66" width="8" height="8" fill="white" opacity="0.3"/>
                    </svg>
                </div>
                <div>
                    <h1>Smart Screening</h1>
                    <span class="brand-sub">Program screening patient<br>Maharaj Nakorn Chiang Mai Hospital 2026</span>
                </div>
            </div>
            <div class="top-bar-right">
                <div class="nav-links">
                    <a href="summary.php" class="nav-link">
                        <i class="fas fa-th-list"></i> ตารางรวมทุก OPD
                    </a>
                    <a href="directory.php" class="nav-link">
                        <i class="fas fa-phone-alt"></i> สมุดเบอร์โทรศัพท์
                    </a>
                </div>
                <div class="top-bar-status">
                    <span class="status-dot"></span>
                    <span class="status-text">ระบบพร้อมใช้งาน</span>
                </div>
            </div>
        </div>
    </header>

    <main id="app">
        <!-- Filter Bar -->
        <section id="filter-section">
            <div class="filter-bar">
                <!-- Age Group -->
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fa-solid fa-user"></i> กลุ่มอายุ
                        <span class="optional-tag">(ไม่บังคับ)</span>
                    </label>
                    <div class="age-options">
                        <label class="age-btn">
                            <input type="radio" name="age" value="<15">
                            <span>&lt; 15 ปี (เด็ก)</span>
                        </label>
                        <label class="age-btn">
                            <input type="radio" name="age" value=">=15">
                            <span>≥ 15 ปี (ผู้ใหญ่)</span>
                        </label>
                    </div>
                </div>

                <!-- Day -->
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="fa-solid fa-calendar-day"></i> วันรับบริการ
                        <span class="optional-tag">(ไม่บังคับ)</span>
                    </label>
                    <div class="day-options" id="day-selector">
                        <button type="button" class="day-btn" data-day="Monday">จันทร์</button>
                        <button type="button" class="day-btn" data-day="Tuesday">อังคาร</button>
                        <button type="button" class="day-btn" data-day="Wednesday">พุธ</button>
                        <button type="button" class="day-btn" data-day="Thursday">พฤหัสบดี</button>
                        <button type="button" class="day-btn" data-day="Friday">ศุกร์</button>
                    </div>
                    <input type="hidden" id="selected-day" value="">
                </div>

                <!-- Keyword -->
                <div class="filter-group filter-group-search">
                    <label class="filter-label">
                        <i class="fa-solid fa-magnifying-glass"></i> ค้นหาอาการ / Diagnosis / คลินิก
                        <span class="optional-tag">(ไม่บังคับ)</span>
                    </label>
                    <div class="search-wrap">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" id="keyword" placeholder="พิมพ์คำค้นหา เช่น Keloid, CVT, นิ่ว, Cardio..." autocomplete="off">
                        <button type="button" id="clear-keyword" title="ล้างคำค้นหา" style="display:none;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <div id="suggest-dropdown" class="suggest-dropdown"></div>
                    </div>
                </div>

                <button id="search-btn" type="button">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>ค้นหาห้องตรวจ</span>
                </button>
            </div>
        </section>

        <!-- Results -->
        <section id="results-section">
            <div class="results-header">
                <h2><i class="fa-solid fa-clipboard-list"></i> ผลการค้นหา</h2>
                <span id="result-count" class="count-badge">0 รายการ</span>
            </div>
            <div id="results-container">
                <div class="empty-state">
                    <i class="fa-solid fa-arrow-up-from-bracket"></i>
                    <p class="empty-title">เลือกเงื่อนไขด้านบน แล้วกด "ค้นหาห้องตรวจ"</p>
                    <p class="empty-sub">สามารถค้นหาจากอาการ/คลินิกอย่างเดียว หรือเลือกวัน/อายุ ก็ได้</p>
                </div>
            </div>
        </section>
    </main>

    <script>
    $(function(){
        const dayTH = {Monday:'จันทร์',Tuesday:'อังคาร',Wednesday:'พุธ',Thursday:'พฤหัสบดี',Friday:'ศุกร์'};

        // Day selector — click to toggle (can deselect)
        $('.day-btn').click(function(){
            if($(this).hasClass('active')){
                $(this).removeClass('active');
                $('#selected-day').val('');
            } else {
                $('.day-btn').removeClass('active');
                $(this).addClass('active');
                $('#selected-day').val($(this).data('day'));
            }
        });

        // Age selector — click again to deselect
        $('.age-btn span').click(function(e){
            const radio = $(this).prev('input[type=radio]');
            if(radio.prop('checked')){
                e.preventDefault();
                radio.prop('checked', false);
            }
        });

        // Autocomplete logic
        let suggestTimer = null;
        $('#keyword').on('input', function(){
            const val = $(this).val().trim();
            $('#clear-keyword').toggle(val.length > 0);
            clearTimeout(suggestTimer);
            if(val.length >= 1){
                suggestTimer = setTimeout(()=>{
                    $.getJSON('suggest.php', {q: val}, function(data){
                        const dd = $('#suggest-dropdown');
                        if(!data.length){ dd.hide().empty(); return; }
                        let html = '';
                        data.forEach(item=>{
                            const icon = item.type==='clinic' ? 'fa-hospital' : 'fa-notes-medical';
                            const label = item.type==='clinic' ? 'คลินิก' : 'อาการ';
                            html += `<div class="suggest-item" data-text="${item.text.replace(/"/g,'&quot;')}">
                                <i class="fa-solid ${icon} suggest-icon"></i>
                                <span class="suggest-text">${item.text}</span>
                                <span class="suggest-type">${label}</span>
                            </div>`;
                        });
                        dd.html(html).show();
                    });
                }, 200);
            } else {
                $('#suggest-dropdown').hide().empty();
            }
        });

        // Click suggestion
        $(document).on('click', '.suggest-item', function(){
            $('#keyword').val($(this).data('text'));
            $('#suggest-dropdown').hide().empty();
            $('#clear-keyword').show();
            performSearch();
        });

        // Hide dropdown on click outside
        $(document).click(function(e){
            if(!$(e.target).closest('.search-wrap').length){
                $('#suggest-dropdown').hide();
            }
        });

        $('#clear-keyword').click(function(){
            $('#keyword').val('').focus();
            $(this).hide();
            $('#suggest-dropdown').hide().empty();
        });

        // Enter key
        $('#keyword').keypress(function(e){
            if(e.which===13){
                $('#suggest-dropdown').hide();
                performSearch();
            }
        });

        // Search button
        $('#search-btn').click(performSearch);

        function performSearch(){
            const ageRadio = $('input[name="age"]:checked');
            const age = ageRadio.length ? ageRadio.val() : '';
            const day = $('#selected-day').val();
            const keyword = $('#keyword').val().trim();
            const container = $('#results-container');
            const countBadge = $('#result-count');

            // Must have at least one filter
            if(!age && !day && !keyword){
                container.html(`
                    <div class="empty-state">
                        <i class="fa-solid fa-filter"></i>
                        <p class="empty-title">กรุณาเลือกเงื่อนไขอย่างน้อย 1 ข้อ</p>
                        <p class="empty-sub">เลือกอายุ หรือ วัน หรือ พิมพ์อาการ/คลินิก</p>
                    </div>`);
                countBadge.text('0 รายการ');
                return;
            }

            // Loading
            $('#search-btn').addClass('loading').prop('disabled',true);
            container.html('<div class="loading-state"><div class="spinner"></div><span>กำลังค้นหา...</span></div>');

            $.ajax({
                url:'search.php', type:'POST',
                data:{age,day,keyword}, dataType:'json',
                success:function(data){
                    $('#search-btn').removeClass('loading').prop('disabled',false);
                    countBadge.text(data.length+' รายการ');

                    if(!data.length){
                        container.html(`
                            <div class="empty-state">
                                <i class="fa-regular fa-rectangle-list"></i>
                                <p class="empty-title">ไม่พบ OPD ที่ตรงกับเงื่อนไข</p>
                                <p class="empty-sub">ลองเปลี่ยนวัน กลุ่มอายุ หรือคำค้นหาใหม่</p>
                            </div>`);
                        return;
                    }

                    // Check if we need to show day column (when day is not filtered)
                    const showDay = (day === '');

                    let html='';
                    data.forEach((item,i)=>{
                        const clinic = item.clinic||'ทั่วไป';
                        const time = item.time_slot||'ตามเวลาทำการ';
                        const tel = item.tel||'-';
                        const diag = item.diagnosis_matched && item.diagnosis_matched!=='-' ? item.diagnosis_matched : '';
                        const ageDisplay = item.age_group||'';
                        const dayDisplay = dayTH[item.day_week]||item.day_week||'';

                        let warnHtml='';
                        if(item.warnings&&item.warnings.length){
                            item.warnings.forEach(w=>{
                                if(!w) return;
                                let t = w.replace(/(consult|Consult|CONSULT)/gi,'<strong class="consult-hl">$1</strong>');
                                warnHtml+=`<div class="warning-box"><i class="fa-solid fa-triangle-exclamation"></i><div class="warning-text">${t}</div></div>`;
                            });
                        }

                        let remarkHtml='';
                        if(item.remark){
                            remarkHtml=`<div class="remark-box"><i class="fa-solid fa-circle-info"></i><span>${item.remark}</span></div>`;
                        }

                        // Day badge (show when day filter is not set)
                        let dayBadge = '';
                        if(showDay && dayDisplay){
                            dayBadge = `<div class="day-badge ${item.day_week}">${dayDisplay}</div>`;
                        }

                        // Age badge
                        let ageBadge = '';
                        if(ageDisplay){
                            ageBadge = `<div class="age-badge-tag">${ageDisplay === '>=15' ? '≥15 ปี' : '<15 ปี'}</div>`;
                        }

                        html+=`
                        <div class="result-card" style="animation-delay:${i*0.04}s">
                            <div class="card-top">
                                <div class="opd-badge">OPD ${item.opd}</div>
                                <div class="clinic-name">${clinic}</div>
                                <div class="card-top-tags">
                                    ${dayBadge}
                                    ${ageBadge}
                                    <div class="time-badge"><i class="fa-regular fa-clock"></i> ${time}</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="info-row">
                                    <div class="info-item">
                                        <span class="info-label">เบอร์ติดต่อ</span>
                                        <span class="info-value tel">${tel}</span>
                                    </div>
                                    ${diag?`<div class="info-item"><span class="info-label">เงื่อนไขที่พบ</span><span class="info-value">${diag}</span></div>`:''}
                                </div>
                                ${remarkHtml}
                                ${warnHtml}
                            </div>
                        </div>`;
                    });
                    container.html(html);
                },
                error:function(){
                    $('#search-btn').removeClass('loading').prop('disabled',false);
                    container.html('<div class="error-state"><i class="fa-solid fa-plug-circle-xmark"></i><p>เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่</p></div>');
                }
            });
        }
    });
    </script>
</body>
</html>
