{{-- Reusable image cropper for any <input type="file" data-crop>. Uses Cropper.js. --}}
<div id="akz-crop-modal" style="position:fixed;inset:0;z-index:10000;display:none;align-items:center;justify-content:center;background:rgba(15,23,42,.65);padding:1rem;">
    <div style="background:#fff;border-radius:1rem;max-width:600px;width:100%;box-shadow:0 20px 60px -10px rgba(0,0,0,.4);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
            <p style="font-weight:700;color:#0f172a;font-size:1rem;">Crop image</p>
            <span id="akz-crop-count" style="font-size:.75rem;color:#64748b;"></span>
        </div>
        <div style="padding:1rem;background:#f8fafc;">
            <div style="max-height:60vh;"><img id="akz-crop-img" style="max-width:100%;display:block;"></div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:.5rem;padding:1rem 1.25rem;border-top:1px solid #e2e8f0;">
            <button type="button" id="akz-crop-skip" style="border-radius:.75rem;padding:.5rem 1rem;font-size:.875rem;font-weight:600;color:#475569;background:#f1f5f9;">Use original</button>
            <button type="button" id="akz-crop-cancel" style="border-radius:.75rem;padding:.5rem 1rem;font-size:.875rem;font-weight:600;color:#475569;background:#fff;border:1px solid #e2e8f0;">Cancel</button>
            <button type="button" id="akz-crop-save" style="border-radius:.75rem;padding:.5rem 1.1rem;font-size:.875rem;font-weight:700;color:#fff;background:#2563eb;">Crop &amp; use</button>
        </div>
    </div>
</div>

<script>
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Cropper === 'undefined' || typeof DataTransfer === 'undefined') return;

        var modal = document.getElementById('akz-crop-modal');
        var imgEl = document.getElementById('akz-crop-img');
        var countEl = document.getElementById('akz-crop-count');
        if (!modal) return;

        var cropper = null;
        var input = null;      // input being processed
        var ratio = NaN;       // aspect ratio (NaN = free)
        var queue = [];        // remaining original files
        var result = [];       // cropped/kept files
        var currentFile = null;

        function openCropper(dataUrl) {
            imgEl.src = dataUrl;
            modal.style.display = 'flex';
            if (cropper) { cropper.destroy(); cropper = null; }
            cropper = new Cropper(imgEl, { aspectRatio: ratio, viewMode: 1, autoCropArea: 1, background: false, movable: true, zoomable: true });
        }

        function hideModal() {
            modal.style.display = 'none';
            if (cropper) { cropper.destroy(); cropper = null; }
        }

        function finish() {
            hideModal();
            if (!input) return;
            var dt = new DataTransfer();
            result.forEach(function (f) { dt.items.add(f); });
            input.files = dt.files;

            var sel = input.getAttribute('data-crop-preview');
            if (sel && result.length) {
                var p = document.querySelector(sel);
                if (p) { p.src = URL.createObjectURL(result[result.length - 1]); p.classList.remove('hidden'); }
            }
            input = null;
        }

        function next() {
            if (!queue.length) { finish(); return; }
            currentFile = queue.shift();
            countEl.textContent = queue.length ? ('+' + queue.length + ' more') : '';
            if (!currentFile.type || currentFile.type.indexOf('image/') !== 0) { result.push(currentFile); next(); return; }
            var reader = new FileReader();
            reader.onload = function (e) { openCropper(e.target.result); };
            reader.readAsDataURL(currentFile);
        }

        document.getElementById('akz-crop-save').addEventListener('click', function () {
            if (!cropper) return;
            var canvas = cropper.getCroppedCanvas({ maxWidth: 2400, maxHeight: 2400, imageSmoothingQuality: 'high' });
            canvas.toBlob(function (blob) {
                var name = (currentFile && currentFile.name ? currentFile.name : 'image').replace(/\.[^.]+$/, '') + '.png';
                result.push(new File([blob], name, { type: 'image/png' }));
                next();
            }, 'image/png', 0.92);
        });

        // Keep the original uncropped file.
        document.getElementById('akz-crop-skip').addEventListener('click', function () {
            if (currentFile) result.push(currentFile);
            next();
        });

        // Abort the whole selection.
        document.getElementById('akz-crop-cancel').addEventListener('click', function () {
            if (input) input.value = '';
            input = null; queue = []; result = [];
            hideModal();
        });

        document.querySelectorAll('input[type="file"][data-crop]').forEach(function (el) {
            el.addEventListener('change', function () {
                var files = Array.prototype.slice.call(el.files || []);
                if (!files.length) return;
                input = el;
                var r = parseFloat(el.getAttribute('data-crop-ratio'));
                ratio = (!r || isNaN(r)) ? NaN : r;
                queue = files;
                result = [];
                next();
            });
        });
    });
})();
</script>
