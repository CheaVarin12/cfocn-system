window.html2pdf = require("./html2pdf");
window.$htmlGenerateToPdf = (blockIdHtml, nameFile, cb) => {
  var opt = {
    margin: 0.3,
    filename: `invoice.pdf`,
    image: {
      type: "jpeg",
      quality: 0.98,
    },
    html2canvas: { scale: 2, useCORS: true, dpi: 192, letterRendering: true },
    jsPDF: {
      unit: "in",
      format: "a4",
      orientation: "portrait",
      putTotalPages: true,
    },
  };
  html2pdf()
    .set(opt)
    .from(blockIdHtml)
    .outputPdf()
    .then((pdf) => {
      let getDataBase64 = window.btoa(pdf);
      const fileBlob = b64toBlob(getDataBase64, "application/octet-stream");
      const file = new File([fileBlob], `${nameFile}.pdf`);
      cb(file ?? null);
    });
};
window.b64toBlob = (b64Data, contentType) => {
  contentType = contentType || "";
  var sliceSize = 512;
  b64Data = b64Data.replace(/^[^,]+,/, "");
  b64Data = b64Data.replace(/\s/g, "");
  var byteCharacters = window.atob(b64Data);
  var byteArrays = [];

  for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
    var slice = byteCharacters.slice(offset, offset + sliceSize);

    var byteNumbers = new Array(slice.length);
    for (var i = 0; i < slice.length; i++) {
      byteNumbers[i] = slice.charCodeAt(i);
    }

    var byteArray = new Uint8Array(byteNumbers);

    byteArrays.push(byteArray);
  }
  return new Blob(byteArrays, {
    type: contentType,
  });
};
