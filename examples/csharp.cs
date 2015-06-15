private static long GetTimestamp(int minutes)
{

   return Convert.ToInt64(DateTime.UtcNow.Subtract(DateTime.SpecifyKind(new DateTime(1970, 1, 1, 0, 0, 0), DateTimeKind.Utc)).TotalSeconds) + (minutes * 60);

}

private static String Sign(String data, String key)
{
  Encoding encoding = new UTF8Encoding();

  HMACSHA1 signature = new HMACSHA1(encoding.GetBytes(key));

  return Convert.ToBase64String(signature.ComputeHash(

  encoding.GetBytes(data.ToCharArray()))).Replace('+', '-').Replace('/', '_');

}

 
String apiHost = "http://api.sports-it.com";
String path = String.Format("/api/{0}?Expires={1}", api, GetTimestamp(15).ToString());


String sig = Sign(path, privateKey);
String signedUrl = String.Format("{0}{1}&signature={2}&publickey={3}", apiHost, path, sig, publicKey);
